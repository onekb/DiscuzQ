<?php

/**
 * Discuz & Tencent Cloud
 * This is NOT a freeware, use is subject to license terms
 */

namespace App\Console\Commands\Upgrades;

use App\Models\Attachment;
use App\Models\Order;
use App\Models\Post;
use App\Models\Question;
use App\Models\Thread;
use App\Models\ThreadTag;
use App\Models\User;
use Discuz\Foundation\Application;
use Discuz\Console\AbstractCommand;
use Exception;

/**
 * question 指定人问答帖迁移脚本
 *
 * @package App\Console\Commands\Upgrades
 */
class QuestionMigrationCommand extends AbstractCommand
{
    protected $signature = 'upgrade:questionMigration';

    protected $description = '指定人问答帖数据迁移';

    public function __construct(Application $app)
    {
        parent::__construct();

        $this->app = $app;
        $this->db = app('db');
    }

    public function handle()
    {
        $page = 1;
        $limit = 500;
        $oldQuestionData = self::getOldQuestionData($page, $limit);
        $this->info('指定人问答帖数据迁移start');
        app('log')->info('指定人问答帖数据迁移start');

        $i = 1;
        try {
            while (!empty($oldQuestionData)){
                $this->info('指定人问答帖数据迁移第'. $i .'轮开始');
                app('log')->info('指定人问答帖数据迁移第'. $i .'轮开始');
                $questionData = $oldQuestionData;
                $threadIds = array_column($oldQuestionData, 'thread_id');
                $answerUserIds = array_column($oldQuestionData, 'be_user_id');
                $oldQuestionData = array_column($oldQuestionData, null, 'thread_id');
                $post = Post::query()->whereIn('thread_id', $threadIds)->where('is_first', 1)->get();
                $firstPostData = array_column($post->toArray(), null, 'thread_id');
                $userData = User::query()
                    ->select('id', 'username')
                    ->whereIn('id', $answerUserIds)
                    ->get()->toArray();
                $userData = array_column($userData, null, 'id');

                $this->db->beginTransaction();

                // content主体内容增加@标签
                $this->info('指定人问答帖数据迁移第'. $i .'轮开始--content主体内容增加@标签');
                app('log')->info('指定人问答帖数据迁移第'. $i .'轮开始--content主体内容增加@标签');
                $post->map(function ($item) use($oldQuestionData, $userData) {
                    if (isset($oldQuestionData[$item->thread_id])) {
                        if (isset($userData[$oldQuestionData[$item->thread_id]['be_user_id']])) {
                            $be_user_id = $oldQuestionData[$item->thread_id]["be_user_id"];
                            $usermention = '<p><USERMENTION id="'.$be_user_id.'">@'. $userData[$oldQuestionData[$item->thread_id]['be_user_id']]['username'] . '</USERMENTION></p> ';
                            $item->content = substr_replace($item->content, $usermention, 3, 0);
                            //将 <t> 替换成 <r>
                            $item->content = str_replace(['<t>', '</t>'], ['<r>', '</r>'], $item->content);
                            $item->save();
                        }
                    }
                });



                // 指定人的回答新增为第一条评论
                $this->info('指定人问答帖数据迁移第'. $i .'轮开始--增加指定人回答的评论');
                app('log')->info('指定人问答帖数据迁移第'. $i .'轮开始--增加指定人回答的评论');
                $insert_thread_tags = [];
                foreach ($oldQuestionData as $key => $value) {
                    if (!empty($value['content'])) {
                        if (isset($firstPostData[$key]) && isset($userData[$value['be_user_id']])) {
                            $newPost = new Post();
                            $newPost->user_id = $value['be_user_id'];
                            $newPost->thread_id = $key;
                            $newPost->reply_post_id = $firstPostData[$key]['id'];
                            $newPost->content = '<r><p>'.$value['content'].'</p></r>';
                            $newPost->ip = $value['ip'];
                            $newPost->port = $value['port'];
                            $newPost->created_at = $firstPostData[$key]['created_at'];
                            $newPost->updated_at = $firstPostData[$key]['updated_at'];
                            $newPost->is_first = 0;
                            $newPost->is_comment = 0;
                            $newPost->is_approved = 1;
                            $newPost->save();

                            // 更改图片关联
                            $attachmentData = Attachment::query()
                                ->where('type_id', $value['id'])
                                ->where('type', Attachment::TYPE_OF_ANSWER)
                                ->update(['type_id' => $newPost->id, 'type' => Attachment::TYPE_OF_IMAGE]);
                        }
                    }
                }
                Thread::query()->whereIn('id', $threadIds)->update(['type' => 0]);
                // 将付费围观订单改为打赏订单
                $this->info('指定人问答帖数据迁移第'. $i .'轮开始--修改围观订单');
                app('log')->info('指定人问答帖数据迁移第'. $i .'轮开始--修改围观订单');
                $onlookerOrderData = Order::query()
                    ->where('type', Order::ORDER_TYPE_ONLOOKER)
                    ->whereIn('thread_id', $threadIds)
                    ->update(['type' => Order::ORDER_TYPE_REWARD]);

                $this->db->commit();
                $this->info('指定人问答帖数据迁移第'. $i .'轮结束');
                app('log')->info('指定人问答帖数据迁移第'. $i .'轮结束');

                $i += 1;
                $page += 1;
                $oldQuestionData = self::getOldQuestionData($page, $limit);
            }
        } catch (\Exception $e){
            $this->db->rollBack();
            $this->info('处理指定人问答帖数据迁移，数据库出错，错误记录：' . $e->getMessage());
            app('log')->info('处理指定人问答帖数据迁移，数据库出错', [$e->getMessage()]);
        }
        $this->info('指定人问答帖数据迁移end');
        app('log')->info('指定人问答帖数据迁移end');
    }

    public function getOldQuestionData($page, $limit)
    {
        $offset = ($page - 1) * $limit;
        return Question::query()->whereNotNull('thread_id')->offset($offset)->limit($limit)->get()->toArray();
    }
}
