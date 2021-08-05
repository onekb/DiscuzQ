<?php

/**
 * Copyright (C) 2020 Tencent Cloud.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace App\Console\Commands\Upgrades;

use Discuz\Console\AbstractCommand;
use Discuz\Foundation\Application;
use Illuminate\Database\ConnectionInterface;
/**
 * thread 迁移脚本，迁移数据库  thread_tag、thread_tom，其中帖子中图文混排中的图片情况先不管，只考虑单独添加的图片/附件
 */
class NotificationsMigrationCommand extends AbstractCommand
{


    protected $signature = 'notifications:migration';

    protected $description = '通知内容数据库迁移';

    protected $notifications_dst;
    protected $notifications;

    protected $app;

    protected $db;

    protected $db_pre;


    /**
     * AvatarCleanCommand constructor.
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        parent::__construct();
        $this->app = $app;
        $this->db = app('db');
        $database = $this->app->config('database');
        $this->db_pre = $database['prefix'];

        //该脚本会操作到的相关表
        $this->notifications = $this->db_pre.'notifications';
        $this->notifications_dst = $this->db_pre.'notifications_bakv2';
    }


    public function handle()
    {
        app('log')->info('开始消息数据迁移start');
        $this->info('开始消息数据迁移start');
        //建个临时表备份原始数据
        app(ConnectionInterface::class)->statement(app(ConnectionInterface::class)->raw(self::notificationsSql()));

        app(ConnectionInterface::class)->statement(app(ConnectionInterface::class)->raw(self::addBackSql()));
        // 找出 host/details 的数据
        $host = $_SERVER['HTTP_HOST'];
        $host_sql_detail = $host.'\\\\/details';
        $host_php_detail = $host.'\/details';
        $host_thread = $host.'\/thread';
        $list = $this->db->table('notifications')->where('data', 'like', "%{$host_sql_detail}%")->get(['id','data']);
        foreach ($list as $val){
            $val->data = str_replace("{$host_php_detail}", "{$host_thread}", $val->data);
            $this->db->table('notifications')->where('id', $val->id)->update(['data' => $val->data]);
        }
        app('log')->info('开始消息数据迁移end');
        $this->info('开始消息数据迁移end');
    }


    //add_sql
    public function addBackSql(){
        return "INSERT INTO {$this->notifications_dst} (`id`,`type`,`notifiable_type`,`notifiable_id`,`data`,`read_at`,`created_at`,`updated_at`) select `id`,`type`,`notifiable_type`,`notifiable_id`,`data`,`read_at`,`created_at`,`updated_at` from {$this->notifications} ";
    }

    //notifications_sql
    public function notificationsSql(){
        return  "CREATE TABLE {$this->notifications_dst} (
                    `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '通知 id',
                    `type` VARCHAR(255) NOT NULL COMMENT '通知类型' COLLATE 'utf8mb4_unicode_ci',
                    `notifiable_type` VARCHAR(255) NOT NULL COLLATE 'utf8mb4_unicode_ci',
                    `notifiable_id` BIGINT(20) UNSIGNED NOT NULL,
                    `data` TEXT NOT NULL COMMENT '通知内容' COLLATE 'utf8mb4_unicode_ci',
                    `read_at` DATETIME NULL DEFAULT NULL COMMENT '通知阅读时间',
                    `created_at` DATETIME NOT NULL COMMENT '创建时间',
                    `updated_at` DATETIME NOT NULL COMMENT '更新时间',
                    PRIMARY KEY (`id`) USING BTREE,
                    INDEX `notifications_notifiable_type_notifiable_id_index` (`notifiable_type`, `notifiable_id`) USING BTREE
                )
                COLLATE='utf8mb4_unicode_ci'
                ENGINE=InnoDB
                AUTO_INCREMENT=117624";
    }


}
