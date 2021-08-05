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

use App\Models\Post;
use App\Models\PostMod;
use App\Models\PostUser;
use Discuz\Console\AbstractCommand;
use Discuz\Foundation\Application;
use Illuminate\Database\ConnectionInterface;
/**
 * thread 迁移脚本，迁移数据库  thread_tag、thread_tom，其中帖子中图文混排中的图片情况先不管，只考虑单独添加的图片/附件
 */
class PostForeignKeyCommand extends AbstractCommand
{


    protected $signature = 'postForeignKey:update';

    protected $description = '更新与posts相关的外键';

    // 数据迁移涉及到的表 start
    protected $posts;
    protected $post_mentions_user;
    protected $post_mod;
    protected $post_user;
    //end

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
        $this->posts = $this->db_pre. (new Post())->getTable();
        $this->post_mentions_user = $this->db_pre. 'post_mentions_user';
        $this->post_mod = $this->db_pre. (new PostMod())->getTable();
        $this->post_user = $this->db_pre. (new PostUser())->getTable();
    }


    public function handle()
    {
        app('log')->info('更新posts相关外键start');
        $this->info('更新posts相关外键start');
        //与 posts 表有外键关联的需要注意：post_mentions_user --> post_mentions_user_post_id_foreign(post_id)  --> posts (id)
        // post_mod --> post_mod_post_id_foreign(post_id) --> posts (id)
        // post_user --> post_user_post_id_foreign(post_id) --> posts (id)
        // 1、先删除对应的外键
        app(ConnectionInterface::class)->statement(app(ConnectionInterface::class)->raw("alter table {$this->post_mentions_user} drop foreign key {$this->db_pre}post_mentions_user_post_id_foreign"));
        app(ConnectionInterface::class)->statement(app(ConnectionInterface::class)->raw("alter table {$this->post_mod} drop foreign key {$this->db_pre}post_mod_post_id_foreign"));
        app(ConnectionInterface::class)->statement(app(ConnectionInterface::class)->raw("alter table {$this->post_user} drop foreign key {$this->db_pre}post_user_post_id_foreign"));
        // 2、由于 更新后的posts表过滤了很多脏数据  post，所以 post_mentions_user、post_mod、post_user 这三个表删除相关脏数据
        app(ConnectionInterface::class)->statement(app(ConnectionInterface::class)->raw("delete from {$this->post_mentions_user} where post_id not in ( select id from {$this->posts} )"));
        app(ConnectionInterface::class)->statement(app(ConnectionInterface::class)->raw("delete from {$this->post_mod} where post_id not in ( select id from {$this->posts} )"));
        app(ConnectionInterface::class)->statement(app(ConnectionInterface::class)->raw("delete from {$this->post_user} where post_id not in ( select id from {$this->posts} )"));
        // 3、添加对应的外键
        app(ConnectionInterface::class)->statement(app(ConnectionInterface::class)->raw("alter table {$this->post_mentions_user} add constraint {$this->db_pre}post_mentions_user_post_id_foreign foreign key(`post_id`) references {$this->posts}(`id`) ON UPDATE RESTRICT ON DELETE CASCADE "));
        app(ConnectionInterface::class)->statement(app(ConnectionInterface::class)->raw("alter table {$this->post_mod} add constraint {$this->db_pre}post_mod_post_id_foreign foreign key(post_id) references {$this->posts}(id) ON UPDATE RESTRICT ON DELETE CASCADE"));
        app(ConnectionInterface::class)->statement(app(ConnectionInterface::class)->raw("alter table {$this->post_user} add constraint {$this->db_pre}post_user_post_id_foreign foreign key(post_id) references {$this->posts}(id) ON UPDATE RESTRICT ON DELETE CASCADE"));
        app('log')->info('更新posts相关外键end');
        $this->info('更新posts相关外键end');
    }

}
