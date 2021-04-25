<?php

/**
 * Discuz & Tencent Cloud
 * This is NOT a freeware, use is subject to license terms
 */

namespace App\Console\Commands\Upgrades;

use App\Models\NotificationTpl;
use App\Notifications\Messages\TemplateVariables;
use Discuz\Console\AbstractCommand;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Database\Eloquent\Collection;
use NotificationTplSeeder;
use Throwable;

class AddNoticeTplCommand extends AbstractCommand
{
    use TemplateVariables;
    use NoticeTrait;

    protected $signature = 'upgrade:noticeAdd {--i|init}';

    protected $description = 'Initialization/new notification type data format.';

    /**
     * @var string
     */
    protected $currentType;

    /**
     * @var ConnectionInterface
     */
    protected $connection;

    /**
     * @var NotificationTplSeeder
     */
    protected $notificationTplSeeder;

    /**
     * @var Collection $tplAll
     */
    protected $tplAll;

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
        if ($this->option('init')) {
            $this->initData();
        } else {
            $data = $this->notificationTplSeeder->getFilterTpl();
            $this->tplAll = NotificationTpl::all();

            if (! $this->tplAll->isEmpty()) {
                try {
                    $this->connection->transaction(function () use ($data) {

                        // check iteration data
                        if ($this->checkIterationBeforeData($this->tplAll)) {
                            // change type_name
                            $this->sortOutUnite();
                        }

                        collect($data)->each(function ($item) {
                            /**
                             * 当新通知不存在时，执行添加
                             */
                            if (empty($this->tplAll->where('type_name', $item['type_name'])->where('type', $item['type'])->first())) {
                                // 设置当前通知类型
                                $this->setCurrentType($item['type']);

                                /** @var NotificationTpl $create */
                                $create = NotificationTpl::query()->create($item);

                                // point out
                                $message = '插入' . $this->currentType . '通知: ' . ' [notice_id] => ' . $create->notice_id . ' [名称] => ' . $create->type_name;
                                $this->comment($message);
                            }
                        });
                    }, 2);
                } catch (Throwable $e) {
                    $this->error($e->getMessage());

                    // 回滚事务
                    $this->connection->rollback();

                    // 初始化
                    $this->error('');
                    $this->error('脚本已终止 原因：无法插入新通知，请去数据库核对通知表是否存在重复 notice_id ，或者输入 init 初始化通知数据表');
                    $this->initData();
                }
            } else {
                $this->initData('没有查询到数据，');
                $this->comment('已初始化完成...');
            }
        }

        $this->info('');
        $this->info('脚本执行 [完成]');
    }

    public function initData($supplement = '')
    {
        // 获取表名
        $notice = new NotificationTpl;
        $tableName = config('database.connections.mysql.prefix') . $notice->getTable();

        // 初始化
        if ($this->ask($supplement . '是否需要初始化通知模板数据表(' . $tableName . ')？ 同意请输入init') == 'init') {
            NotificationTpl::query()->delete();

            // 重建自增ID
            $autoNum = $notice->count();
            $sql = 'alter table ' . $tableName . ' auto_increment = ' . $autoNum;
            $this->connection->statement($sql);

            // php disco db:seed --class NotificationTplSeeder
            $this->notificationTplSeeder->run();
        }
    }

    /**
     * 传输当前该条数据类型，设置 info 值用于脚本输出打印
     *
     * @param $type
     */
    public function setCurrentType($type)
    {
        switch ($type) {
            case NotificationTpl::SYSTEM_NOTICE:
                $this->currentType = '*系统*';
                break;
            case NotificationTpl::WECHAT_NOTICE:
                $this->currentType = '[微信]';
                break;
            case NotificationTpl::SMS_NOTICE:
                $this->currentType = '[短信]';
                break;
            case NotificationTpl::ENTERPRISE_WECHAT_NOTICE:
                $this->currentType = '[企业微信]';
                break;
            case NotificationTpl::MINI_PROGRAM_NOTICE:
                $this->currentType = '[小程序]';
                break;
            default:
                $this->currentType = '「未知」';
                break;
        }
    }

    public function sortOutUnite()
    {
        $this->tplAll->each(function ($item) {
            // 判断是否修改过数据库/或判断是否是老版本数据表
            if (isset($this->originConfigTypeName[$item->id])) {
                $originName = $item->type_name;                 // 原 name
                $name = $this->originConfigTypeName[$item->id]; // 变更 name
                if ($originName != $name) {
                    $item->type_name = $name;
                    $item->save();
                    // 输出
                    $msg = "修改'$item->title'(id=$item->id): 【type_name修改】-> [$originName] -> [$name]";
                    $this->comment($msg);
                }
            }
        });
    }

}
