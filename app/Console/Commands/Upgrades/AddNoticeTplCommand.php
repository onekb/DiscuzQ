<?php

/**
 * Discuz & Tencent Cloud
 * This is NOT a freeware, use is subject to license terms
 */

namespace App\Console\Commands\Upgrades;

use App\Models\NotificationTpl;
use Discuz\Console\AbstractCommand;
use Exception;
use Illuminate\Database\ConnectionInterface;
use NotificationTplSeeder;
use Throwable;

class AddNoticeTplCommand extends AbstractCommand
{
    protected $signature = 'upgrade:notice';

    protected $description = 'Initialization/new notification type data format.';

    protected $table;

    protected $isFirst = false;

    protected $connection;

    /**
     * @var string
     */
    protected $currentType;

    /**
     * @var NotificationTplSeeder
     */
    protected $notificationTplSeeder;

    /**
     * AddNoticeTplCommand constructor.
     *
     * @param ConnectionInterface $connection
     * @param string|null $name
     * @param $notificationTplSeeder
     */
    public function __construct(ConnectionInterface $connection, string $name = null, NotificationTplSeeder $notificationTplSeeder)
    {
        parent::__construct($name);

        $this->connection = $connection;
        $this->notificationTplSeeder = $notificationTplSeeder;
    }

    public function handle()
    {
        $data = $this->notificationTplSeeder->addData();

        try {
            $this->connection->transaction(function () use ($data) {
                collect($data)->each(function ($item, $key) {
                    /**
                     * 当新通知不存在时，执行添加
                     */
                    if (! NotificationTpl::query()->where('id', $key)->exists()) {
                        // 设置当前通知类型
                        $this->setCurrentType($item['type']);

                        // 添加数据
                        $tplId = NotificationTpl::query()->insertGetId($item);

                        // 验证数据库自增值是否对应通知ID值
                        if ($tplId != $key) {
                            // 删除刚刚插入的数据
                            NotificationTpl::query()->where(['id' => $tplId])->delete();

                            $this->comment('');
                            $noticeName = $item['type_name'];
                            $this->error('存在无法对应自增ID的通知：' . "[$key $noticeName]");
                            $this->error('应插入通知ID：' . $key);
                            $this->error('实际数据库自增值已到达：' . $tplId);
                            if (! $this->confirm('是否重置自增值，尝试重新添加')) {
                                $this->error('');
                                $this->error('脚本已终止 原因：[通知表内容有改动，无法对应通知ID]');
                            }

                            /**
                             * 尝试再次循环插入该条
                             */
                            $this->tryAgainInsert($item, $key);

                        } else {
                            // 插入成功后输出
                            $this->pointOut('insert', $tplId, $item['type_name'], 'comment');
                        }
                    }
                });
            }, 2);
        } catch (Throwable $e) {
            $this->error($e->getMessage());

            // 回滚事务
            $this->connection->rollback();
        }

        $this->info('');
        $this->info('脚本执行 [完成]');
    }

    /**
     * 尝试重置ID后再次 执行添加通知内容
     *
     * @param $item
     * @param $key
     * @param bool $recursion 是否是递归
     * @throws Exception
     */
    public function tryAgainInsert($item, $key, $recursion = false)
    {
        // 重建自增ID
        $notice = new NotificationTpl;
        $tableName = config('database.connections.mysql.prefix') . $notice->getTable();
        $autoNum = $notice->count();
        $sql = 'alter table ' . $tableName . ' auto_increment = ' . $autoNum;
        $this->connection->statement($sql);

        $tplId = NotificationTpl::query()->insertGetId($item);

        if ($tplId != $key) {
            // 删除刚刚插入的数据
            NotificationTpl::query()->where(['id' => $tplId])->delete();

            // 检测是否存在自定义通知数据
            $all = NotificationTpl::query()->get();
            $ids = array_column($all->toArray(), 'id');
            $lashId = array_pop($ids); // 获取最后一个ID

            if ($lashId >= $key && $recursion == false) {
                $this->error('');
                $this->error('检测到存在自定义过的通知内容');
                if ($this->ask('是否还原通知表，执行更新数据？ 同意请输入remove') == 'remove') {
                    // 执行删除
                    /** @var NotificationTpl $delData */
                    $delData = NotificationTpl::query()->where('id', '>=', $key)->get();
                    if ($delData->isNotEmpty()) {
                        NotificationTpl::query()->whereIn('id', array_column($delData->toArray(), 'id'))->delete();

                        // 删除通知后后输出
                        $delIds = array_column($delData->toArray(), 'id');
                        $delIds = implode('、', $delIds);
                        $nameArr = array_column($delData->toArray(), 'type_name');
                        $nameArr = implode('、', $nameArr);
                        $this->pointOut('delete', $delIds, $nameArr, 'comment');
                    }
                    // 递归再次尝试添加
                    $this->tryAgainInsert($item, $key, true);
                } else {
                    $this->error('');
                    $this->error('脚本已终止 原因：[通知表数据无法初始化，请手动操作删除自定义的通知]');
                    throw new Exception();
                }
            } else {
                $this->error('');
                $this->error('脚本已终止 原因：[无法插入新通知，请去数据库核对通知ID是否对应自增ID]');
                if ($this->ask('是否需要初始化该数据表？ 同意请输入init') == 'init') {
                    $this->notificationTplSeeder->run(); // php disco db:seed --class NotificationTplSeeder
                }
                throw new Exception();
            }
        } else {
            // 插入成功后输出
            $this->pointOut('insert', $tplId, $item['type_name'], 'comment');
        }
    }

    /**
     * @param $mode
     * @param $id
     * @param $name
     * @param $method
     */
    public function pointOut($mode, $id, $name, $method)
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

        $msg = $modeName . $this->currentType . '通知: ' . ' id:' . $id . ' 名称[' . $name . ']';
        $this->{$method}($msg);
    }

    /**
     * 传输当前该条数据类型，设置 info 值用于脚本输出打印
     *
     * @param $type
     */
    public function setCurrentType($type)
    {
        if ($type == NotificationTpl::SYSTEM_NOTICE) {
            $this->currentType = '*系统*';
        } elseif ($type == NotificationTpl::WECHAT_NOTICE) {
            $this->currentType = '[微信]';
        } else {
            $this->currentType = '非系统';
        }
    }

}
