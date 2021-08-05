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

namespace App\Console\Commands;

use App\Formatter\Formatter;
use App\Models\Attachment;
use App\Models\Order;
use App\Models\Post;
use App\Models\PostGoods;
use App\Models\PostMod;
use App\Models\PostUser;
use App\Models\Question;
use App\Models\Thread;
use App\Models\ThreadRedPacket;
use App\Models\ThreadReward;
use App\Models\ThreadTag;
use App\Models\ThreadTom;
use App\Models\ThreadVideo;
use App\Models\Topic;
use App\Models\User;
use App\Repositories\ThreadVideoRepository;
use Carbon\Carbon;
use Discuz\Console\AbstractCommand;
use Discuz\Foundation\Application;
use Discuz\Qcloud\QcloudTrait;
use Illuminate\Database\DatabaseManager;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Facade;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Support\Facades\Schema;
/**
 * thread 迁移脚本，迁移数据库  thread_tag、thread_tom，其中帖子中图文混排中的图片情况先不管，只考虑单独添加的图片/附件
 */
class ThreadMigrationCommand extends AbstractCommand
{


    protected $signature = 'thread:migration';

    protected $description = '帖子内容数据库迁移';

    // 数据迁移涉及到的表 start
    protected $threads;
    protected $thread_tag;
    protected $posts;
    protected $thread_tom;
    protected $thread_red_packets;
    protected $attachments;
    protected $thread_video;
    protected $users;
    protected $post_content_temp;
    protected $posts_dst;
    protected $post_mentions_user;
    protected $post_mod;
    protected $post_user;
    //end

    protected $app;

    protected $db;

    protected $db_pre;

    protected $old_type;

    protected $attachment_type;

    protected $video_type;

    const V3_TYPE = 99;

    const LIMIT = 500;

    const TEMP_SQL = 'CREATE TABLE `post_content_temp` (
  `id` bigint(20) unsigned NOT NULL,
  `content` mediumtext COLLATE utf8mb4_unicode_ci DEFAULT NULL
  ) ENGINE=InnoDB AUTO_INCREMENT=101821 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci';

    const AGG_SQL = 'INSERT INTO `posts_dst` (`id`,`user_id`,`thread_id`,`reply_post_id`,`reply_user_id`,`comment_post_id`,`comment_user_id`,`content`,`ip`,`port`,`reply_count`,`like_count`,`created_at`,`updated_at`,`deleted_at`,`deleted_user_id`,`is_first`, `is_comment`, `is_approved`) select `posts`.`id`,`posts`.`user_id`,`posts`.`thread_id`,`posts`.`reply_post_id`,`posts`.`reply_user_id`,`posts`.`comment_post_id`,`posts`.`comment_user_id`,`post_content_temp`.`content`,`posts`.`ip`,`posts`.`port`,`posts`.`reply_count`,`posts`.`like_count`,`posts`.`created_at`,`posts`.`updated_at`,`posts`.`deleted_at`,`posts`.`deleted_user_id`,`posts`.`is_first`, `posts`.`is_comment`, `posts`.`is_approved` from post_content_temp inner join posts on post_content_temp.id = posts.id WHERE post_content_temp.id NOT IN (SELECT id FROM posts_dst)';

    //与 posts 表有外键关联的需要注意：post_mentions_user --> post_mentions_user_post_id_foreign  --> posts (id)
    // post_mod --> post_mod_post_id_foreign --> posts (id)
    // post_user --> post_user_post_id_foreign --> posts (id)
    //
    const POST_SQL = 'CREATE TABLE `posts_dst` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT \'回复 id\',
  `user_id` bigint(20) unsigned DEFAULT NULL COMMENT \'发表用户 id\',
  `thread_id` bigint(20) unsigned DEFAULT NULL COMMENT \'关联主题 id\',
  `reply_post_id` bigint(20) unsigned DEFAULT NULL COMMENT \'回复 id\',
  `reply_user_id` bigint(20) unsigned DEFAULT NULL COMMENT \'回复用户 id\',
  `comment_post_id` bigint(20) unsigned DEFAULT NULL COMMENT \'评论回复 id\',
  `comment_user_id` bigint(20) unsigned DEFAULT NULL COMMENT \'评论回复用户 id\',
  `content` mediumtext COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT \'内容\',
  `ip` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT \'\' COMMENT \'ip 地址\',
  `port` int(10) unsigned NOT NULL DEFAULT 0 COMMENT \'端口\',
  `reply_count` int(10) unsigned NOT NULL DEFAULT 0 COMMENT \'关联回复数\',
  `like_count` int(10) unsigned NOT NULL DEFAULT 0 COMMENT \'喜欢数\',
  `created_at` datetime NOT NULL COMMENT \'创建时间\',
  `updated_at` datetime NOT NULL COMMENT \'更新时间\',
  `deleted_at` datetime DEFAULT NULL COMMENT \'删除时间\',
  `deleted_user_id` bigint(20) unsigned DEFAULT NULL COMMENT \'删除用户 id\',
  `is_first` tinyint(3) unsigned NOT NULL DEFAULT 0 COMMENT \'是否首个回复\',
  `is_comment` tinyint(3) unsigned NOT NULL DEFAULT 0 COMMENT \'是否是回复回帖的内容\',
  `is_approved` tinyint(3) unsigned NOT NULL DEFAULT 1 COMMENT \'是否合法\',
  PRIMARY KEY (`id`),
  KEY `posts_temp_thread_id_index` (`thread_id`) USING BTREE,
  KEY `posts_temp_user_id_foreign` (`user_id`),
  KEY `posts_temp_deleted_user_id_foreign` (`deleted_user_id`),
  KEY `posts_temp_reply_post_id` (`reply_post_id`) USING BTREE,
  KEY `posts_temp_reply_post_id_index` (`reply_post_id`),
  CONSTRAINT `posts_temp_deleted_user_id_foreign` FOREIGN KEY (`deleted_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `posts_temp_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=101821 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci';


    /**
     * AvatarCleanCommand constructor.
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        parent::__construct();
        $this->app = $app;
        $this->db = app('db');
        $this->old_type = [
            Thread::TYPE_OF_TEXT,
            Thread::TYPE_OF_LONG,
            Thread::TYPE_OF_VIDEO,
            Thread::TYPE_OF_IMAGE,
            Thread::TYPE_OF_AUDIO,
            Thread::TYPE_OF_QUESTION,
            Thread::TYPE_OF_GOODS
        ];


        $this->attachment_type = [
            Attachment::TYPE_OF_FILE    =>  ThreadTag::DOC,
            Attachment::TYPE_OF_IMAGE   =>  ThreadTag::IMAGE,
            Attachment::TYPE_OF_ANSWER  =>  ThreadTag::REWARD       // 问答的类型迁移数据时全定义为 悬赏问答 的类型
        ];

        $this->video_type = [
            ThreadVideo::TYPE_OF_VIDEO  =>  ThreadTag::VIDEO,
            ThreadVideo::TYPE_OF_AUDIO  =>  ThreadTag::VOICE
        ];
        $database = $this->app->config('database');
        $this->db_pre = $database['prefix'];

        //该脚本会操作到的相关表
        $this->threads = $this->db_pre. (new Thread())->getTable();
        $this->thread_tag = $this->db_pre. (new ThreadTag())->getTable();
        $this->posts = $this->db_pre. (new Post())->getTable();
        $this->thread_tom = $this->db_pre. (new ThreadTom())->getTable();
        $this->thread_red_packets = $this->db_pre. (new ThreadRedPacket())->getTable();
        $this->attachments = $this->db_pre. (new Attachment())->getTable();
        $this->thread_video = $this->db_pre. (new ThreadVideo())->getTable();
        $this->users = $this->db_pre. (new User())->getTable();
        $this->post_content_temp = $this->db_pre. 'post_content_temp';
        $this->posts_dst = $this->db_pre. 'posts_dst';
        $this->post_mentions_user = $this->db_pre. 'post_mentions_user';
        $this->post_mod = $this->db_pre. (new PostMod())->getTable();
        $this->post_user = $this->db_pre. (new PostUser())->getTable();
    }

    protected function mysql_escape($fieldValue)
    {
        if (!empty($fieldValue) && is_string($fieldValue)) {
            return str_replace(
                ['\\', "\0", "\n", "\r", "'", '"', "\x1a"],
                ['\\\\', '\\0', '\\n', '\\r', "\\'", '\\"', '\\Z'],
                $fieldValue
            );
        }

        return $fieldValue;
    }

    public function handle()
    {
        app('log')->info('开始帖子数据迁移start');
        $this->info('开始帖子数据迁移start');

        app(ConnectionInterface::class)->statement(app(ConnectionInterface::class)->raw("DROP TABLE IF EXISTS {$this->post_content_temp}"));
        app(ConnectionInterface::class)->statement(app(ConnectionInterface::class)->raw("DROP TABLE IF EXISTS {$this->posts_dst}"));

        //迁移tag数据信息
        app('log')->info('迁移 thread_tag start');
        app(ConnectionInterface::class)->statement(app(ConnectionInterface::class)->raw("INSERT INTO {$this->thread_tag} (thread_id, tag, created_at, updated_at) SELECT id,?,created_at,updated_at FROM {$this->threads} where type = ? AND id NOT IN (SELECT thread_id FROM {$this->thread_tag} WHERE tag = ?)"), [ThreadTag::TEXT, Thread::TYPE_OF_TEXT, ThreadTag::TEXT]);
        app(ConnectionInterface::class)->statement(app(ConnectionInterface::class)->raw("INSERT INTO {$this->thread_tag} (thread_id, tag, created_at, updated_at) SELECT id,?,created_at,updated_at FROM {$this->threads} where type = ? AND id NOT IN (SELECT thread_id FROM {$this->thread_tag} WHERE tag = ?)"), [ThreadTag::TEXT, Thread::TYPE_OF_LONG, ThreadTag::TEXT]);
        app(ConnectionInterface::class)->statement(app(ConnectionInterface::class)->raw("INSERT INTO {$this->thread_tag} (thread_id, tag, created_at, updated_at) SELECT id,?,created_at,updated_at FROM {$this->threads} where type = ? AND id NOT IN (SELECT thread_id FROM {$this->thread_tag} WHERE tag = ?)"), [ThreadTag::VIDEO, Thread::TYPE_OF_VIDEO, ThreadTag::VIDEO]);
        app(ConnectionInterface::class)->statement(app(ConnectionInterface::class)->raw("INSERT INTO {$this->thread_tag} (thread_id, tag, created_at, updated_at) SELECT id,?,created_at,updated_at FROM {$this->threads} where type = ? AND id NOT IN (SELECT thread_id FROM {$this->thread_tag} WHERE tag = ?)"), [ThreadTag::IMAGE, Thread::TYPE_OF_IMAGE, ThreadTag::IMAGE]);
        app(ConnectionInterface::class)->statement(app(ConnectionInterface::class)->raw("INSERT INTO {$this->thread_tag} (thread_id, tag, created_at, updated_at) SELECT id,?,created_at,updated_at FROM {$this->threads} where type = ? AND id NOT IN (SELECT thread_id FROM {$this->thread_tag} WHERE tag = ?)"), [ThreadTag::VOICE, Thread::TYPE_OF_AUDIO, ThreadTag::VOICE]);
        app(ConnectionInterface::class)->statement(app(ConnectionInterface::class)->raw("INSERT INTO {$this->thread_tag} (thread_id, tag, created_at, updated_at) SELECT id,?,created_at,updated_at FROM {$this->threads} where type = ? AND id NOT IN (SELECT thread_id FROM {$this->thread_tag} WHERE tag = ?)"), [ThreadTag::REWARD, Thread::TYPE_OF_QUESTION, ThreadTag::REWARD]);
        app(ConnectionInterface::class)->statement(app(ConnectionInterface::class)->raw("INSERT INTO {$this->thread_tag} (thread_id, tag, created_at, updated_at) SELECT id,?,created_at,updated_at FROM {$this->threads} where type = ? AND id NOT IN (SELECT thread_id FROM {$this->thread_tag} WHERE tag = ?)"), [ThreadTag::GOODS, Thread::TYPE_OF_GOODS, ThreadTag::GOODS]);
        app(ConnectionInterface::class)->statement(app(ConnectionInterface::class)->raw("INSERT INTO {$this->thread_tag} (thread_id, tag, created_at, updated_at) SELECT thread_id,?,created_at,updated_at FROM {$this->thread_red_packets} WHERE thread_id NOT IN (SELECT thread_id FROM {$this->thread_tag} WHERE tag = ?)"), [ThreadTag::RED_PACKET, ThreadTag::RED_PACKET]);

        app(ConnectionInterface::class)->statement(app(ConnectionInterface::class)->raw("INSERT INTO {$this->thread_tag} (thread_id, tag, created_at, updated_at) select distinct {$this->posts}.thread_id,?, {$this->posts}.created_at,{$this->posts}.updated_at from {$this->attachments} inner join {$this->posts} on {$this->attachments}.type_id = {$this->posts}.id where {$this->posts}.is_first = 1 and {$this->attachments}.type= ? and  {$this->posts}.thread_id NOT IN (SELECT thread_id FROM {$this->thread_tag} WHERE tag = ?)"), [ThreadTag::DOC, Attachment::TYPE_OF_FILE, ThreadTag::DOC]);

        app(ConnectionInterface::class)->statement(app(ConnectionInterface::class)->raw("INSERT INTO {$this->thread_tag} (thread_id, tag, created_at, updated_at) select distinct {$this->posts}.thread_id,?, {$this->posts}.created_at,{$this->posts}.updated_at from ({$this->attachments} inner join {$this->posts} on {$this->attachments}.type_id = {$this->posts}.id) inner join {$this->threads} on {$this->posts}.thread_id = {$this->threads}.id where {$this->posts}.is_first = 1 and {$this->attachments}.type in (1,4,5) and {$this->threads}.type <> ? and {$this->posts}.thread_id NOT IN (SELECT thread_id FROM {$this->thread_tag} WHERE tag = ?)"), [ThreadTag::IMAGE, Thread::TYPE_OF_IMAGE, ThreadTag::IMAGE]);
        app('log')->info('迁移 thread_tag end');

        //迁移红包
        app('log')->info('迁移红包帖 thread_tom start');
        self::migrateRedPacket();
        app('log')->info('迁移红包帖 thread_tom end');
        //迁移图片
        app('log')->info('迁移图片帖 thread_tom start');
        $imageStringStart = '\'{\"imageIds\":[\'';
        $imageStringEnd = '\']}\'';
        app(ConnectionInterface::class)->statement(app(ConnectionInterface::class)->raw("INSERT INTO {$this->thread_tom} (thread_id,tom_type,{$this->thread_tom}.key,{$this->thread_tom}.status,created_at,updated_at,{$this->thread_tom}.value) select {$this->posts}.thread_id,?,?,IF({$this->threads}.deleted_at, -1, 0),{$this->threads}.created_at,{$this->threads}.updated_at,concat({$imageStringStart}, group_concat({$this->attachments}.id order by {$this->attachments}.order,{$this->attachments}.id), {$imageStringEnd}) as value from {$this->attachments} inner join {$this->posts} on {$this->attachments}.type_id = {$this->posts}.id inner join {$this->threads} on {$this->posts}.thread_id = {$this->threads}.id where {$this->posts}.is_first = 1 and {$this->attachments}.type in (1,4,5) and {$this->posts}.thread_id not in (select thread_id from {$this->thread_tom} where tom_type = ? )  group by {$this->posts}.thread_id"), [ThreadTag::IMAGE,ThreadTag::IMAGE, ThreadTag::IMAGE]);
        app('log')->info('迁移图片帖 thread_tom end');
        //迁移附件
        app('log')->info('迁移附件帖 thread_tom start');
        $docStringStart = '\'{\"docIds\":[\'';
        $docStringEnd = '\']}\'';
        app(ConnectionInterface::class)->statement(app(ConnectionInterface::class)->raw("INSERT INTO {$this->thread_tom} (thread_id,tom_type,{$this->thread_tom}.key,{$this->thread_tom}.status,created_at,updated_at,{$this->thread_tom}.value) select {$this->posts}.thread_id,?,?,IF({$this->threads}.deleted_at, -1, 0),{$this->threads}.created_at,{$this->threads}.updated_at,concat({$docStringStart}, group_concat({$this->attachments}.id order by {$this->attachments}.order,{$this->attachments}.id), {$docStringEnd}) as value from {$this->attachments} inner join {$this->posts} on {$this->attachments}.type_id = {$this->posts}.id inner join {$this->threads} on {$this->posts}.thread_id = {$this->threads}.id where {$this->posts}.is_first = 1 and {$this->attachments}.type = 0 and {$this->posts}.thread_id not in (select thread_id from {$this->thread_tom} where tom_type = ? ) group by {$this->posts}.thread_id"), [ThreadTag::DOC,ThreadTag::DOC, ThreadTag::DOC]);
        app('log')->info('迁移附件帖 thread_tom end');

        //迁移视频
        app('log')->info('迁移视频帖 thread_tom start');
        self::migrateVideo();
        app('log')->info('迁移视频帖 thread_tom end');

        //迁移音频
        app('log')->info('迁移音频帖 thread_tom start');
        self::migrateAudio();
        app('log')->info('迁移音频帖 thread_tom end');

        //迁移问答
        app('log')->info('迁移问答帖 thread_tom start');
        self::migrateQuestion();
        app('log')->info('迁移问答帖 thread_tom end');

        //迁移商品帖
        app('log')->info('迁移商品帖 thread_tom start');
        self::migrateGoods();
        app('log')->info('迁移商品帖 thread_tom end');

        app('log')->info('数据迁移end');

        // 创建 post_content_temp 临时表 id、 content
        app(ConnectionInterface::class)->statement(app(ConnectionInterface::class)->raw(self::tempSql()));
        //app(ConnectionInterface::class)->statement(app(ConnectionInterface::class)->raw(self::BAK_SQL));
        // 创建 posts_dst 临时表 ，复制一份 posts 表
        app(ConnectionInterface::class)->statement(app(ConnectionInterface::class)->raw(self::postSql()));
        //v3数据迁移之后，下面的操作会比较刺激 -- 修改 posts 中的 content 字段数据
        $page = 1;
        $limit = 500;
        app('log')->info('修改posts中content数据start');
        $data = self::getOldData($limit);
        $i = 0;
        try {
            while (!empty($data)){
                app('log')->info('修改posts中content数据start，开始次数：'.$i);
                $i ++;
                $thread_array = array();
                $post_array = array();
                $this->db->beginTransaction();
                foreach ($data as $key => $val){
                    foreach ($val as $vi){
                        $content = $vi['content'];
                        if(empty($content))     continue;
                        $content = self::s9eRender($content);
                        $content = self::v3Content($content);
                        $post_array[] = ['id' => $vi['post_id'], 'content' => $content];

                    }
                    $thread_array[] = $key;
                }

                $res = $this->db->table('post_content_temp')->insert($post_array);
                if($res === false){
                    $this->db->rollBack();
                    $this->info('插入 post_content_temp 出错');
                    break;
                }

                //最后将 posts 对应的 thread 的 type 修改为 99
                $res = $this->db->table('threads')->whereIn('id', $thread_array)->update(['type' => self::V3_TYPE]);
                if($res === false){
                    $this->db->rollBack();
                    $this->info('修改 threads 中 type 出错');
                    break;
                }
                app(ConnectionInterface::class)->statement(app(ConnectionInterface::class)->raw(self::addSql()));
                $this->db->commit();

                unset($thread_array);
                unset($post_array);
                $page += 1;
                $data = self::getOldData($limit);
            }
            app('log')->info('data完成', [$data]);
        }catch (\Exception $e){
            $this->db->rollBack();
            $this->info($e->getMessage());
            app('log')->info('处理posts的content，数据库出错', [$e->getMessage()]);
            return;
        }
        app('log')->info('post 数据转化至 post_dst 完成，开始切换表名');
        app(ConnectionInterface::class)->statement(app(ConnectionInterface::class)->raw("rename TABLE {$this->posts} to posts_bakv2"));
        app(ConnectionInterface::class)->statement(app(ConnectionInterface::class)->raw("rename TABLE {$this->posts_dst} to {$this->posts}"));
        app('log')->info('帖子内容 posts 的 content 修改完成');
        app('log')->info('开始帖子数据迁移end');
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
        app(ConnectionInterface::class)->statement(app(ConnectionInterface::class)->raw("alter table {$this->post_mentions_user} add constraint {$this->db_pre}post_mentions_user_post_id_foreign foreign key(`post_id`) references {$this->posts}(`id`)"));
        app(ConnectionInterface::class)->statement(app(ConnectionInterface::class)->raw("alter table {$this->post_mod} add constraint {$this->db_pre}post_mod_post_id_foreign foreign key(post_id) references {$this->posts}(id)"));
        app(ConnectionInterface::class)->statement(app(ConnectionInterface::class)->raw("alter table {$this->post_user} add constraint {$this->db_pre}post_user_post_id_foreign foreign key(post_id) references {$this->posts}(id)"));
        app('log')->info('更新 posts 相关外键成功');


        $this->info('开始帖子数据迁移end');
    }


    public function migrateRedPacket(){
        $start_page = 0;
        $isset_red_thread_ids = ThreadTom::query()->where('tom_type', ThreadTag::RED_PACKET)->pluck('thread_id')->toArray();
        while (!empty($list = self::getThreadRedPacket($start_page, $isset_red_thread_ids)) && !empty($list->toArray())){
            $this->db->beginTransaction();
            $res = array();
            $thread_red_ids = [];
            foreach ($list as $thread_red_packets){
                //还需要插入对应的  thread_tom
//                $order = Order::where(['thread_id' => $val->id, 'type' => Order::ORDER_TYPE_TEXT])->first();
                $value = [
                    'thread_id' => $thread_red_packets->id,
                    'post_id'   => $thread_red_packets->post_id,
                    'rule'  =>  $thread_red_packets->rule,
                    'condition' =>  $thread_red_packets->condition,
                    'likenum'   =>  $thread_red_packets->likenum,
                    'money'     =>  $thread_red_packets->money,
                    'remain_money'  =>  $thread_red_packets->remain_money,
                    'number'    =>  $thread_red_packets->number,
                    'remain_number' =>  $thread_red_packets->remain_number,
                    'status'    =>  $thread_red_packets->status,
                    'updated_at'    =>  $thread_red_packets->updated_at,
                    'created_at'    =>  $thread_red_packets->created_at,
                    'id'        =>  $thread_red_packets->thread_red_packet_id,
                    'content'   =>  '红包帖'
                ];
                $value = json_encode($value);
                $res[] = self::collectThreadTom($thread_red_packets, ThreadTag::RED_PACKET, $value);
                $thread_red_ids[] = $thread_red_packets->id;
            }
            if(!empty($res)){
                $result = self::batchInsertThreadTom($res);
                if (!$result) {
                    $this->db->rollBack();
                    $this->error('thread_tom red_packet error. thread data is : '.json_encode($res->toArray()));
                    break;
                }
            }
            $this->db->commit();
            $isset_red_thread_ids = array_merge($isset_red_thread_ids, $thread_red_ids);
            $start_page ++;
        }
    }

    public function migrateVideo(){
        //先刷新已经转码的
        $videoStringStart = '\'{\"videoId\":\'';
        $videoStringEnd = '\'}\'';
        app(ConnectionInterface::class)->statement(app(ConnectionInterface::class)->raw("INSERT INTO {$this->thread_tom} (thread_id,tom_type,{$this->thread_tom}.key,{$this->thread_tom}.status,created_at,updated_at,{$this->thread_tom}.value) select {$this->threads}.id,?,?,IF({$this->threads}.deleted_at, -1, 0),{$this->threads}.created_at,{$this->threads}.updated_at,concat({$videoStringStart},{$this->thread_video}.id, {$videoStringEnd}) from {$this->thread_video} inner join {$this->threads} on {$this->thread_video}.thread_id = {$this->threads}.id where {$this->thread_video}.type = 0 and {$this->thread_video}.status = 1 and {$this->threads}.is_draft = 0 and {$this->thread_video}.thread_id not in (select thread_id from {$this->thread_tom} where tom_type = ?)"), [ThreadTag::VIDEO,ThreadTag::VIDEO, ThreadTag::VIDEO]);

        //刷新未转码的
        app(ConnectionInterface::class)->statement(app(ConnectionInterface::class)->raw("INSERT INTO {$this->thread_tom} (thread_id,tom_type,{$this->thread_tom}.key,{$this->thread_tom}.status,created_at,updated_at,{$this->thread_tom}.value) select {$this->threads}.id,?,?,IF({$this->threads}.deleted_at, -1, 0),{$this->threads}.created_at,{$this->threads}.updated_at,concat({$videoStringStart},MAX({$this->thread_video}.id), {$videoStringEnd}) from {$this->thread_video} inner join {$this->threads} on {$this->thread_video}.thread_id = {$this->threads}.id where {$this->thread_video}.type = 0 and {$this->thread_video}.status = 0 and {$this->threads}.is_draft = 1 and {$this->thread_video}.thread_id not in (select thread_id from {$this->thread_tom} where tom_type = ?) group by {$this->thread_video}.thread_id"), [ThreadTag::VIDEO,ThreadTag::VIDEO, ThreadTag::VIDEO]);
    }

    public function migrateAudio(){
        //先刷新已经转码的
        $audioStringStart = '\'{\"audioId\":\'';
        $audioStringEnd = '\'}\'';
        app(ConnectionInterface::class)->statement(app(ConnectionInterface::class)->raw("INSERT INTO {$this->thread_tom} (thread_id,tom_type,{$this->thread_tom}.key,{$this->thread_tom}.status,created_at,updated_at,{$this->thread_tom}.value) select {$this->threads}.id,?,?,IF({$this->threads}.deleted_at, -1, 0),{$this->threads}.created_at,{$this->threads}.updated_at,concat({$audioStringStart},{$this->thread_video}.id, {$audioStringEnd}) from {$this->thread_video} inner join {$this->threads} on {$this->thread_video}.thread_id = {$this->threads}.id where {$this->thread_video}.type = 1 and {$this->thread_video}.status = 1 and {$this->threads}.is_draft = 0 and {$this->thread_video}.thread_id not in (select thread_id from {$this->thread_tom} where tom_type = ?)"), [ThreadTag::VOICE,ThreadTag::VOICE, ThreadTag::VOICE]);

        //刷新未转码的
        app(ConnectionInterface::class)->statement(app(ConnectionInterface::class)->raw("INSERT INTO {$this->thread_tom} (thread_id,tom_type,{$this->thread_tom}.key,{$this->thread_tom}.status,created_at,updated_at,{$this->thread_tom}.value) select {$this->threads}.id,?,?,IF({$this->threads}.deleted_at, -1, 0),{$this->threads}.created_at,{$this->threads}.updated_at,concat({$audioStringStart},MAX({$this->thread_video}.id), {$audioStringEnd}) from {$this->thread_video} inner join {$this->threads} on {$this->thread_video}.thread_id = {$this->threads}.id where {$this->thread_video}.type = 1 and {$this->thread_video}.status = 0 and {$this->threads}.is_draft = 1 and {$this->thread_video}.thread_id not in (select thread_id from {$this->thread_tom} where tom_type = ?) group by {$this->thread_video}.thread_id"), [ThreadTag::VOICE,ThreadTag::VOICE, ThreadTag::VOICE]);
    }

    public function migrateQuestion(){
        $start_page = 0;
        $isset_reward_ids = ThreadTom::query()->where('tom_type', ThreadTag::REWARD)->pluck('thread_id')->toArray();
        while(!empty($list = self::getThreadQuestion($start_page, $isset_reward_ids)) && !empty($list->toArray())){
            $this->db->beginTransaction();
            $res = array();
            $added_reward_ids = [];
            foreach ($list as $val){
                $q_type = 0;
                $q_price = $remain_money = 0;
                $answer_id = 0;
                $q_expired_at = $q_created_at = $q_updated_at = '';
                if(!empty($val->reward_id)){
                    $q_type = $val->type;
                    $q_price = $val->money;
                    $remain_money = $val->remain_money;
                    $q_expired_at = $val->reward_expired_at;
                    $answer_id = $val->answer_id;
                    $q_created_at = $val->reward_created_at;
                    $q_updated_at = $val->reward_updated_at;
                }
                else
                {
                    continue;
                }

                $body = [
                    'thread_id'  =>  $val->id,
                    'post_id'   =>  $val->post_id,
                    'type' =>  $q_type,
                    'user_id' =>  $val->user_id,
                    'answer_id' => $answer_id,
                    'money' =>  $q_price,
                    'remain_money' => $remain_money,
                    'expired_at'    =>  $q_expired_at,
                    'updated_at'    =>  $q_updated_at,
                    'created_at'    =>  $q_created_at,
                    'id'            =>  !empty($val->reward_id) ? $val->reward_id : 0,        //这里放悬赏id
                    'content'       =>  null
                ];
                $value = json_encode($body);
                $res[] = self::collectThreadTom($val, ThreadTag::REWARD, $value);
                $added_reward_ids[] = $val->id;
            }

            if(!empty($res)){
                $result = self::batchInsertThreadTom($res);
                if (!$result) {
                    $this->db->rollBack();
                    $this->error('question insert: thread_tom goods error. thread data is : '.json_encode($val->toArray()));
                    break;
                }
            }
            $this->db->commit();
            $isset_reward_ids = array_merge($isset_reward_ids, $added_reward_ids);
            $start_page ++;
        }
    }

    public function migrateGoods(){
        $start_page = 0;
        $isset_goods_thread_ids = ThreadTom::query()->where('tom_type', ThreadTag::GOODS)->pluck('thread_id')->toArray();
        while (!empty($list = self::getThreadGoods($start_page, $isset_goods_thread_ids)) && !empty($list->toArray())){
            $res = array();
            $this->db->beginTransaction();
            $added_goods_thread_ids = [];
            foreach ($list as $val){
                // 插入 thread_tom ，先插goods，再判断是否插入image类型
                $body = [
                    'id'        =>  $val->gid,
                    'userId'    =>  $val->user_id,
                    'postId'    =>  $val->post_id,
                    'platformId'    =>  $val->platform_id,
                    'title'     =>  $val->title,
                    'price'     =>  $val->price,
                    'imagePath' =>  self::preHttps($val->image_path),
                    'type'      =>  $val->type,
                    'status'    =>  $val->status,
                    'readyContent'  =>  $val->ready_content,
                    'detailContent'    =>  $val->detail_content,
                    'createdAt'     =>  $val->gcreated_at,
                    'updatedAt'     =>  $val->gupdated_at,
                ];
                $value = json_encode($body);
                $res[] = self::collectThreadTom($val, ThreadTag::GOODS, $value);
                $added_goods_thread_ids[] = $val->id;
            }

            if(!empty($res)){
                $result = self::batchInsertThreadTom($res);
                if (!$result) {
                    $this->db->rollBack();
                    $this->error('long attachment insert: thread_tom red_packet error. thread data is : '.json_encode($val->toArray()));
                    break;
                }
            }
            $this->db->commit();
            $isset_goods_thread_ids = array_merge($isset_goods_thread_ids, $added_goods_thread_ids);
            $start_page ++;
        }
    }


    public function preHttps($url){
        if(strpos($url, 'http') === false){
            $url = 'https://'.$url;
        }
        return $url;
    }

    public function insertThreadTom($thread, $tom_type, $value){
        return $this->db->table('thread_tom')->insert([
            'thread_id' =>  $thread->id,
            'tom_type'  =>  $tom_type,
            'key'   =>  $tom_type,
            'value' =>  $value,
            'status'    => !empty($thread->deleted_at) ? -1 : 0,
            'created_at'    =>  $thread->created_at,
            'updated_at'    =>  $thread->updated_at
        ]);
    }



    public function collectThreadTom($thread, $tom_type, $value){
        return [
            'thread_id' =>  $thread->id,
            'tom_type'  =>  $tom_type,
            'key'   =>  $tom_type,
            'value' =>  $value,
            'status'    => !empty($thread->deleted_at) ? -1 : 0,
            'created_at'    =>  $thread->created_at,
            'updated_at'    =>  $thread->updated_at
        ];
    }

    public function batchInsertThreadTom($arr){
        return $this->db->table('thread_tom')->insert($arr);
    }

    //通过s9e，将threads中的 content 转为接口获取的 html 渲染格式
    public function s9eRender($text){
        return $this->app->make(Formatter::class)->render($text);
    }

    //将s9e render 渲染后的数据，正则匹配替换调表情，如不切换，当站长更换域名时，表情url会失效
    public function v3Content($text){
        preg_match_all('/<img.*?emoji\/qq.*?>/i', $text, $m1);
        if(empty($m1[0])){
            return $text;
        }
        $searches = $m1[0];
        $replaces = [];
        foreach ($searches as $key => $search) {
            preg_match('/alt="(.*?)"/i', $search, $m2);
            if(empty($m2[0])){      //没有匹配上
                unset($searches[$key]);
                continue;
            }
            $emoji = preg_replace('/alt="(.*?)"/', '$1', $m2[0]);
            $replaces[] = ':'.$emoji.':';
        }
        $text = str_replace($searches, $replaces, $text);
        return $text;
    }

    public function renderTopic($text){
        preg_match_all('/#.+?#/', $text, $topic);
        if(empty($topic)){
            return  $text;
        }
        $topic = $topic[0];
        $topic = str_replace('#', '', $topic);
        $topics = Topic::query()->select('id', 'content')->whereIn('content', $topic)->get()->map(function ($item) {
            $item['content'] = '#' . $item['content'] . '#';
            $item['html'] = sprintf('<span id="topic" value="%s">%s</span>', $item['id'], $item['content']);
            return $item;
        })->toArray();
        foreach ($topics as $val){
            $text = preg_replace("/{$val['content']}/", $val['html'], $text, 1);
        }
        return $text;
    }

    public function renderCall($text){
        preg_match_all('/@.+? /', $text, $call);
        if(empty($call)){
            return  $text;
        }
        $call = $call[0];
        $call = str_replace(['@', ' '], '', $call);
        $ats = User::query()->select('id', 'username')->whereIn('username', $call)->get()->map(function ($item) {
            $item['username'] = '@' . $item['username'];
            $item['html'] = sprintf('<span id="member" value="%s">%s</span>', $item['id'], $item['username']);
            return $item;
        })->toArray();
        foreach ($ats as $val){
            $text = preg_replace("/{$val['username']}/", "{$val['html']}", $text, 1);
        }
        return $text;
    }

    //获取老数据 threads 、posts
    public function getOldData($limit){
        $data = [];
        $threadIds = Thread::query()->where('type','!=', self::V3_TYPE)
            ->limit($limit)->pluck('id')->toArray();
        if(empty($threadIds))   return $data;
        $posts = Post::query()->whereIn('thread_id', $threadIds)
            ->where('user_id', '!=', 0)
            ->whereNotNull('user_id')
            ->get(['id', 'content', 'thread_id']);
        foreach ($posts as $val){
            $data[$val->thread_id][] = [
                'post_id'   =>  $val->id,
                'content'   =>  $val->content
            ];
        }
        return $data;
    }


    //获取文字贴数据
    public function getThreadRedPacket($start_page, $isset_red_thread_ids){
        return  $this->db->table('thread_red_packets as t')
            ->join('threads', 'threads.id','=','t.thread_id')
            ->join('posts as p','t.thread_id','=','p.thread_id')
            ->where('p.is_first', 1)
            ->whereNotIn('t.thread_id', $isset_red_thread_ids)
            ->offset($start_page * self::LIMIT)
            ->limit(self::LIMIT)
            ->get(['t.thread_id as id','p.id as post_id', 't.rule', 't.condition', 't.likenum','t.money','t.remain_money','t.number','t.remain_number','t.status','threads.updated_at','threads.created_at','threads.deleted_at','t.id as thread_red_packet_id']);
    }

    //获取问题数据
    public function getThreadQuestion($start_page, $isset_reward_ids){
        //这里只处理悬赏帖了
        $thread_reward_ids = ThreadReward::query()->where('type', 0)->pluck('thread_id')->toArray();
        return  $this->db->table('threads as t')
            ->join('posts as p','t.id','=','p.thread_id')
            ->join('thread_rewards as r','p.id','=','r.post_id')
            ->where('t.type', Thread::TYPE_OF_QUESTION)
            ->where('p.is_first', 1)
            ->whereIn('t.id', $thread_reward_ids)
            ->whereNotIn('t.id', $isset_reward_ids)
            ->offset($start_page * self::LIMIT)
            ->limit(self::LIMIT)
            ->get(['t.id','t.created_at','t.deleted_at','t.updated_at','t.user_id','p.id as post_id','r.type','r.money','r.remain_money','r.expired_at as reward_expired_at','r.answer_id','r.created_at as reward_created_at','r.updated_at as reward_updated_at','r.id as reward_id']);
    }

    //获取商品数据
    public function getThreadGoods($start_page, $isset_goods_thread_ids){
        return  $this->db->table('threads as t')
            ->join('posts as p','t.id','=','p.thread_id')
            ->join('post_goods as g','g.post_id','=','p.id')
            ->where('t.type', Thread::TYPE_OF_GOODS)
            ->where('p.is_first', 1)
            ->whereNotIn('t.id', $isset_goods_thread_ids)
            ->offset($start_page * self::LIMIT)
            ->limit(self::LIMIT)
            ->get(['t.id','t.created_at','t.deleted_at','t.updated_at','p.id as post_id', 'g.id as gid', 'g.platform_id', 'g.title', 'g.price', 'g.image_path', 'g.type', 'g.status', 'g.ready_content', 'g.detail_content', 'g.created_at as gcreated_at', 'g.updated_at as gupdated_at', 'g.user_id']);
    }

    //add_sql
    public function addSql(){
        return "INSERT INTO {$this->posts_dst} (`id`,`user_id`,`thread_id`,`reply_post_id`,`reply_user_id`,`comment_post_id`,`comment_user_id`,`content`,`ip`,`port`,`reply_count`,`like_count`,`created_at`,`updated_at`,`deleted_at`,`deleted_user_id`,`is_first`, `is_comment`, `is_approved`) select {$this->posts}.`id`,{$this->posts}.`user_id`,{$this->posts}.`thread_id`,{$this->posts}.`reply_post_id`,{$this->posts}.`reply_user_id`,{$this->posts}.`comment_post_id`,{$this->posts}.`comment_user_id`,{$this->post_content_temp}.`content`,{$this->posts}.`ip`,{$this->posts}.`port`,{$this->posts}.`reply_count`,{$this->posts}.`like_count`,{$this->posts}.`created_at`,{$this->posts}.`updated_at`,{$this->posts}.`deleted_at`,{$this->posts}.`deleted_user_id`,{$this->posts}.`is_first`, {$this->posts}.`is_comment`, {$this->posts}.`is_approved` from {$this->post_content_temp} inner join {$this->posts} on {$this->post_content_temp}.id = {$this->posts}.id WHERE {$this->post_content_temp}.id NOT IN (SELECT id FROM {$this->posts_dst})";
    }

    //posts_sql
    public function postSql(){
        return  "CREATE TABLE {$this->posts_dst} (
                  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '回复 id',
                  `user_id` bigint(20) unsigned DEFAULT NULL COMMENT '发表用户 id',
                  `thread_id` bigint(20) unsigned DEFAULT NULL COMMENT '关联主题 id',
                  `reply_post_id` bigint(20) unsigned DEFAULT NULL COMMENT '回复 id',
                  `reply_user_id` bigint(20) unsigned DEFAULT NULL COMMENT '回复用户 id',
                  `comment_post_id` bigint(20) unsigned DEFAULT NULL COMMENT '评论回复 id',
                  `comment_user_id` bigint(20) unsigned DEFAULT NULL COMMENT '评论回复用户 id',
                  `content` mediumtext COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '内容',
                  `ip` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT 'ip 地址',
                  `port` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '端口',
                  `reply_count` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '关联回复数',
                  `like_count` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '喜欢数',
                  `created_at` datetime NOT NULL COMMENT '创建时间',
                  `updated_at` datetime NOT NULL COMMENT '更新时间',
                  `deleted_at` datetime DEFAULT NULL COMMENT '删除时间',
                  `deleted_user_id` bigint(20) unsigned DEFAULT NULL COMMENT '删除用户 id',
                  `is_first` tinyint(3) unsigned NOT NULL DEFAULT 0 COMMENT '是否首个回复',
                  `is_comment` tinyint(3) unsigned NOT NULL DEFAULT 0 COMMENT '是否是回复回帖的内容',
                  `is_approved` tinyint(3) unsigned NOT NULL DEFAULT 1 COMMENT '是否合法',
                  PRIMARY KEY (`id`),
                  KEY `posts_temp_thread_id_index` (`thread_id`) USING BTREE,
                  KEY `posts_temp_user_id_foreign` (`user_id`),
                  KEY `posts_temp_deleted_user_id_foreign` (`deleted_user_id`),
                  KEY `posts_temp_reply_post_id` (`reply_post_id`) USING BTREE,
                  KEY `posts_temp_reply_post_id_index` (`reply_post_id`),
                  UNIQUE KEY `post_id` (`id`),
                  CONSTRAINT `{$this->db_pre}posts_temp_deleted_user_id_foreign` FOREIGN KEY (`deleted_user_id`) REFERENCES {$this->users} (`id`) ON DELETE SET NULL,
                  CONSTRAINT `{$this->db_pre}posts_temp_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES {$this->users} (`id`) ON DELETE SET NULL
                ) ENGINE=InnoDB AUTO_INCREMENT=101821 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    }

    //temp_sql
    public function tempSql(){
        return  "CREATE TABLE {$this->post_content_temp} (
                  `id` bigint(20) unsigned NOT NULL,
                  `content` mediumtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                   UNIQUE KEY `post_id` (`id`)
                  ) ENGINE=InnoDB AUTO_INCREMENT=101821 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    }

}
