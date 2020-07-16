<?php

/**
 * Discuz & Tencent Cloud
 * This is NOT a freeware, use is subject to license terms
 */

namespace App\Console\Commands\Upgrades;

use Discuz\Console\AbstractCommand;
use App\Models\NotificationTpl as NotificationTplModel;
use Illuminate\Database\ConnectionInterface;

class AddNoticeTplCommand extends AbstractCommand
{
    protected $signature = 'upgrade:notice';

    protected $description = '更新迭代/新增通知类型数据格式';

    protected $table;

    protected $isFirst = false;

    protected $connection;

    /**
     * AddNoticeTplCommand constructor.
     *
     * @param ConnectionInterface $connection
     * @param string|null $name
     */
    public function __construct(ConnectionInterface $connection, string $name = null)
    {
        parent::__construct($name);

        $this->connection = $connection;
    }

    public function handle()
    {
        $data = NotificationTplModel::addData();

        $bar = $this->createProgressBar(count($data));

        $bar->start();

        $this->comment('');

        try {
            $this->connection->transaction(function () use ($data, $bar) {
                collect($data)->each(function ($item, $key) use ($bar) {

                    $where = [
                        'type' => $item['type'],
                        'type_name' => $item['type_name']
                    ];

                    // false 不存在->执行添加
                    if (!NotificationTplModel::where($where)->exists()) {
                        if ($item['type'] == 0) {
                            $info = '*系统*';
                        } elseif ($item['type'] == 1) {
                            $info = '[微信]';
                        } else {
                            $info = '非系统';
                        }

                        $tplId = NotificationTplModel::insertGetId($item);

                        if ($tplId != $key) {
                            // 删除刚刚插入的数据 终止脚本
                            NotificationTplModel::where(['id' => $tplId])->delete();

                            // TODO 重建自增ID
                            // $notice = new NotificationTplModel;
                            // $autoNum = NotificationTplModel::count();
                            // $sql = 'alter table ' . $notice->table . ' auto_increment = ' . $autoNum;
                            // $this->connection->raw($sql);
                            // 再次递归插入
                            // if ($this->isFirst) {
                            //     return false;
                            // }
                            // $this->isFirst = true;
                            // $this->handle();

                            $this->error('');
                            $this->error('脚本已终止,通知表内容有改动,无法对应通知ID 脚本无法执行');

                            // 插入错误抛出信息
                            $this->pointOut('delete', $tplId, $item['type_name'], $info, 'comment');

                            return false;
                        }

                        // 插入成功后输出
                        $this->pointOut('insert', $tplId, $item['type_name'], $info, 'comment');
                    }

                    $bar->advance();
                });
            }, 2);
        } catch (\Throwable $e) {
            // 回滚事务
            $this->connection->rollback();
        }

        $this->question('');
        $this->question('执行新增通知脚本 [完成]');

        $bar->finish();
    }

    /**
     * @param $mode
     * @param $id
     * @param $name
     * @param $info
     * @param $method
     */
    public function pointOut($mode, $id, $name, $info, $method)
    {
        switch ($mode) {
            case 'delete':
                $modeName = '删除';
                break;
            case 'insert':
                $modeName = '插入';
                break;
            default:
                $modeName = 'x';
                break;
        }

        $msg = $modeName . $info . '通知: ' . ' id:' . $id . ' 名称[' . $name . ']';
        $this->{$method}($msg);
    }

}
