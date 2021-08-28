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
use App\Models\Post;
use App\Models\Thread;
use Discuz\Console\AbstractCommand;
use Discuz\Foundation\Application;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Support\Facades\Schema;

/**
 * 修复帖子迁移脚本中断
 */
class ThreadMigrationPlusCommand extends AbstractCommand
{


    protected $signature = 'thread:migration_plus';

    protected $description = '修复帖子内容数据迁移中断';

    // 数据迁移涉及到的表 start
    protected $threads;
    protected $posts;
    protected $posts_dst;
    protected $post_content_temp;

    //end

    protected $app;

    protected $db;

    protected $db_pre;

    const V3_TYPE = 99;

    const LIMIT = 500;


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
        $this->threads = $this->db_pre. (new Thread())->getTable();
        $this->post_content_temp = $this->db_pre. 'post_content_temp';

        $this->posts = $this->db_pre. (new Post())->getTable();
        $this->posts_dst = $this->db_pre. 'posts_dst';
    }


    public function handle()
    {
        app('log')->info('开始修复帖子数据迁移start');
        $this->info('开始修复帖子数据迁移start');
        $handle_max_thread_count = 300;

        try{
            app(ConnectionInterface::class)->statement(app(ConnectionInterface::class)->raw("select count(1) from posts_bakv2"));
            //将 posts_bakv2 表更名为 pre_posts_bakv2 表
            if(!empty($this->db_pre) && !Schema::hasTable('posts_bakv2')){
                app(ConnectionInterface::class)->statement(app(ConnectionInterface::class)->raw("rename TABLE posts_bakv2 to {$this->db_pre}posts_bakv2"));
            }
            $isset_posts_bakv2 = true;
        }catch (\Exception $e){
            $isset_posts_bakv2 = false;
        }
        //先计算帖子总数量
        $thread_counts = Thread::query()->pluck('id')->toArray();
        $per_between = array_chunk($thread_counts, $handle_max_thread_count);
        $handle_end_thread = $handle_max_thread_count - 1;
        foreach ($per_between as $val){
            $threads = Thread::query()->whereBetween('id', [$val[0], $val[$handle_end_thread]])->get();

            if($isset_posts_bakv2){
                //如果 posts_bakv2 表已经存在
                foreach ($threads as $vo){
                    //找出在 posts_bakv2 但是不在 posts 中的帖子
                    $posts_bakv2s = app('db')->table('posts_bakv2')->where('thread_id', $vo->id)->get();
                    $posts = Post::query()->where('thread_id', $vo->id)->get();
                    $posts_bakv2s_ids = $posts_bakv2s->pluck('id')->toArray();
                    $posts_ids = $posts->pluck('id')->toArray();
                    $posts_bakv2s = $posts_bakv2s->keyBy('id')->toArray();
                    $insert_post = [];
                    $need_insert_ids = array_diff($posts_bakv2s_ids, $posts_ids);
                    if(!empty($need_insert_ids)){
                        $this->db->beginTransaction();
                        foreach ($need_insert_ids as $insert_id){
                            $content = $posts_bakv2s[$insert_id]['content'];
                            try {
                                if(!empty($content)){
                                    $content = self::s9eRender($content);
                                    $content = self::v3Content($content);
                                }
                            }catch (\Exception $e){
                                //如果这里报错，说明处理到了升级过程中发的评论了，V3 格式，则保持原数据格式
                                $content = $posts_bakv2s[$insert_id]['content'];
                            }
                            $insert_post[] = [
                                'id'    =>  $posts_bakv2s[$insert_id]['id'],
                                'user_id'    =>  $posts_bakv2s[$insert_id]['user_id'],
                                'thread_id'    =>  $posts_bakv2s[$insert_id]['thread_id'],
                                'reply_post_id'    =>  $posts_bakv2s[$insert_id]['reply_post_id'],
                                'reply_user_id'    =>  $posts_bakv2s[$insert_id]['reply_user_id'],
                                'comment_post_id'    =>  $posts_bakv2s[$insert_id]['comment_post_id'],
                                'comment_user_id'    =>  $posts_bakv2s[$insert_id]['comment_user_id'],
                                'content'    =>  $content,
                                'ip'    =>  $posts_bakv2s[$insert_id]['ip'],
                                'port'    =>  $posts_bakv2s[$insert_id]['port'],
                                'reply_count'    =>  $posts_bakv2s[$insert_id]['reply_count'],
                                'like_count'    =>  $posts_bakv2s[$insert_id]['like_count'],
                                'created_at'    =>  $posts_bakv2s[$insert_id]['created_at'],
                                'updated_at'    =>  $posts_bakv2s[$insert_id]['updated_at'],
                                'deleted_at'    =>  $posts_bakv2s[$insert_id]['deleted_at'],
                                'deleted_user_id'    =>  $posts_bakv2s[$insert_id]['deleted_user_id'],
                                'is_first'    =>  $posts_bakv2s[$insert_id]['is_first'],
                                'is_comment'    =>  $posts_bakv2s[$insert_id]['is_comment'],
                                'is_approved'    =>  $posts_bakv2s[$insert_id]['is_approved'],
                            ];
                        }
                        //先插 posts
                        $res = Post::query()->insert($insert_post);
                        if($res === false){
                            $this->db->rollBack();
                            $this->info('插入posts出错');
                            break;
                        }
                        $res = Thread::query()->where('id', $vo->id)->update(['type' => self::V3_TYPE]);
                        if($res === false){
                            $this->db->rollBack();
                            $this->info('修改 threads 出错');
                            break;
                        }
                        $this->db->commit();
                    }
                }
            }else{
                foreach ($threads as $vo){
                    //如果都没有 posts_bakv2 的话，说明posts都保留这原始的数据，那么这里直接修改对应的 posts 就好了
                    $posts = Post::query()->where('thread_id', $vo->id)->get();
                    $this->db->beginTransaction();
                    foreach ($posts as $vi){
                        $content = $vi->content;
                        if(!empty($content)){
                            $content = self::s9eRender($content);
                            $content = self::v3Content($content);
                        }
                        $vi->content = $content;
                        $res = $vi->save();
                        if($res === false){
                            $this->db->rollBack();
                            $this->info('修改posts出错');
                            break;
                        }
                    }
                    $vo->type = self::V3_TYPE;
                    $res = $vo->save();
                    if($res === false){
                        $this->db->rollBack();
                        $this->info('修改threads出错');
                        break;
                    }
                    $this->db->commit();
                }
            }
        }
        app('log')->info('结束修复帖子数据迁移end');
        $this->info('结束修复帖子数据迁移end');
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



}
