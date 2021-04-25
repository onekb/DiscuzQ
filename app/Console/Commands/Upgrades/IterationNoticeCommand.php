<?php

/**
 * Discuz & Tencent Cloud
 * This is NOT a freeware, use is subject to license terms
 */

namespace App\Console\Commands\Upgrades;

use App\Models\NotificationTpl;
use App\Notifications\Messages\TemplateVariables;
use Discuz\Console\AbstractCommand;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;
use NotificationTplSeeder;

/**
 * Class IterationNoticeCommand
 * (该脚本用于迭代通知数据/逻辑内容)
 *
 * @package App\Console\Commands\Upgrades
 */
class IterationNoticeCommand extends AbstractCommand
{
    use TemplateVariables;
    use NoticeTrait;

    /**
     * @var string
     */
    protected $signature = 'upgrade:noticeIteration';

    /**
     * @var string
     */
    protected $description = 'Upgrade iteration notification.';

    /**
     * @var NotificationTpl
     */
    protected $tpl;

    /**
     * @var NotificationTplSeeder
     */
    protected $notificationTplSeeder;

    /**
     * @var Collection $tplAll
     */
    protected $tplAll;

    public function __construct(NotificationTpl $tpl, NotificationTplSeeder $notificationTplSeeder)
    {
        parent::__construct();

        $this->tpl = $tpl;
        $this->notificationTplSeeder = $notificationTplSeeder;
    }

    /**
     * {@inheritdoc}
     */
    public function handle()
    {
        /**
         * 优先级
         */
        $this->beforeUpdate();

        $this->afterUpdate();

        $this->finishUpdate();
    }

    /*
    |--------------------------------------------------------------------------
    | beForUpdate
    |--------------------------------------------------------------------------
    */
    public function beforeUpdate()
    {
        $this->tplAll = $this->tpl->all();

        // 2021/1/20 重构微信通知模板 版本
        $this->sortOutUnite();
    }

    /**
     * 统一 type_name 名称，使其作为唯一标识
     * 初始化小程序 page_path
     */
    public function sortOutUnite()
    {
        $this->info('执行脚本 -> (1. 统一type_name名称) ... ...');

        try {
            if ($this->checkIterationBeforeData($this->tplAll)) {
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
        } catch (Exception $e) {
            $this->error('type_name/page_path error');
        }

        $this->info('done ... ...');
    }

    /*
    |--------------------------------------------------------------------------
    | afterUpdate
    |--------------------------------------------------------------------------
    */
    public function afterUpdate()
    {
        // 2021/2/3  不再以自增 id 为唯一标识，新增模板唯一标识
        $this->uniquelyNotice();
        // 2021/2/3  用唯一标识初始化，小程序路径地址
        $this->initPagePath();
    }

    /**
     * 添加通知唯一标识
     */
    public function uniquelyNotice()
    {
        $this->info('执行脚本 -> (2. 添加通知唯一标识) ... ...');

        $this->tplAll->each(function ($item) {
            if (empty($item->notice_id)) {
                $noticeId = $this->comparisonUnique($item->type_name, $item->type);
                // checking string
                if (Str::endsWith($noticeId, '.')) {
                    // 输出
                    $msg = "错误'$item->title'(id=$item->id): 【type_name有变动】-> [该条数据有改动，无法添加模板唯一标识，可执行初始化命令恢复表数据]";
                    $this->error($msg);
                } elseif ($noticeId == null) {
                    // 输出
                    $msg = "错误'$item->title'(id=$item->id): 【type_name未定义】-> [该条通知名称未在代码名单中添加，无法添加模板唯一标识，可执行初始化命令恢复表数据]";
                    $this->error($msg);
                } else {
                    $item->notice_id = $noticeId;
                    $item->save();
                    // 输出
                    $msg = "添加'$item->title'(id=$item->id): 【notice_id 为】-> [$noticeId]";
                    $this->comment($msg);
                }
            }
        });

        $this->info('done ... ...');
    }

    /**
     * 初始化小程序模板默认跳转路由
     */
    public function initPagePath()
    {
        $this->info('执行脚本 -> (3. 初始化小程序模板默认跳转路由) ... ...');

        $this->tplAll->each(function ($item) {
            $pagePath = $this->getInitPagePath($item->notice_id);
            if (!empty($pagePath) && empty($item->page_path)) {
                $trans = trans('template_variables.' . $pagePath);
                $item->page_path = $pagePath;
                $item->save();
                // 输出
                $msg = "初始化'$item->title'(notice_id=$item->notice_id): 【pagePath 初始化为】-> [$pagePath] -> [$trans]";
                $this->comment($msg);
            }
        });

        $this->info('done ... ...');
    }

    /*
    |--------------------------------------------------------------------------
    | finishUpdate
    |--------------------------------------------------------------------------
    */
    public function finishUpdate()
    {
        // TODO to do some things

        // ...
        $this->info('脚本执行 [完成]');
    }
}
