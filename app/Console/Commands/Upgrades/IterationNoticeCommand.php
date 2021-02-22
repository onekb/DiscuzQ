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

    /**
     * @var string
     */
    protected $signature = 'upgrade:notice-iteration';

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
        $this->boForUpdate();

        $this->finishUpdate();
    }

    public function boForUpdate()
    {
        // 2021/1/20 重构微信通知模板 版本
        $this->sortOutUnite();

        $this->afterUpdate();
    }

    public function afterUpdate()
    {
        // TODO to do some things
    }

    /**
     * 统一 type_name 名称，使其作为唯一标识
     * 初始化小程序 page_path
     */
    public function sortOutUnite()
    {
        /** @var Collection $tplAll */
        $tplAll = $this->tpl->all();

        try {
            $tplAll->map(function ($item) {
                if (isset($this->configTypeName[$item->id])) {
                    $originName = $item->type_name; // 原 name
                    $name = $this->configTypeName[$item->id]; // 变更 name
                    if ($originName != $name) {
                        $item->type_name = $name;
                        // 输出
                        $msg = "修改'$item->title'(id=$item->id): 【type_name修改】-> [$originName] -> [$name]";
                        $this->comment($msg);
                    }
                } else {
                    $this->error('NotificationTpl 数据表有更改变动，无法统一[type_name] - ' . $item->id);
                }

                if (isset($this->initPagePath[$item->id]) && empty($item->page_path)) {
                    $pagePath = $this->initPagePath[$item->id];
                    $trans = trans('template_variables.' . $pagePath);
                    $item->page_path = $pagePath;
                    // 输出
                    $msg = "初始化'$item->title'(id=$item->id): 【pagePath 初始化为】-> [$pagePath] -> [$trans]";
                    $this->comment($msg);
                }

                $item->save();
            });
        } catch (Exception $e) {
            $this->error('type_name/page_path error');
        }
    }

    public function finishUpdate()
    {
        // TODO to do some things

        // ...
        $this->info('脚本执行 [完成]');
    }
}
