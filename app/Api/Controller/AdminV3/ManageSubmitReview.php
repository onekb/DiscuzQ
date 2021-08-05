<?php
/**
 * Copyright (C) 2021 Tencent Cloud.
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

namespace App\Api\Controller\AdminV3;

use App\Common\CacheKey;
use App\Events\Post\Saved;
use App\Models\AdminActionLog;
use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use App\Models\UserActionLogs;
use App\Notifications\Related;
use App\Traits\ThreadNoticesTrait;
use App\Traits\PostNoticesTrait;
use App\Common\ResponseCode;
use App\Repositories\UserRepository;
use App\Models\Thread;
use Carbon\Carbon;
use Discuz\Auth\Exception\PermissionDeniedException;
use Discuz\Base\DzqController;
use Discuz\Foundation\EventsDispatchTrait;
use Illuminate\Contracts\Events\Dispatcher;

class ManageSubmitReview extends DzqController
{

    use ThreadNoticesTrait;
    use PostNoticesTrait;
    use EventsDispatchTrait;

    protected function checkRequestPermissions(UserRepository $userRepo)
    {
        if (!$this->user->isAdmin()) {
            throw new PermissionDeniedException('没有权限');
        }
        return true;
    }

    public function main()
    {
        $user = $this->user;
        if (!$user->isAdmin()) {
            $this->outPut(ResponseCode::UNAUTHORIZED,'');
        }

        $data = $this->inPut('data');
        $type = $this->inPut('type'); //1主题 2评论
        if (empty($data) || !is_array($data) || empty($type)) {
            $this->outPut(ResponseCode::INVALID_PARAMETER,'');
        }

        $logArr = [];

        switch ($type) {
            case 1:
                $logArr = $this->threads();
                break;
            case 2:
                $logArr = $this->posts();
                break;
        }

        if (!empty($logArr)) {
            AdminActionLog::insert($logArr);

            $this->outPut(ResponseCode::SUCCESS);
        }

        $this->outPut(ResponseCode::INVALID_PARAMETER);
    }

    public function threads()
    {

        $user = $this->user;
        $data = $this->inPut('data');
        $ids = array_column($data,'id');
        $arr = array_column($data,null,'id');

        $logArr = [];
        $thread = Thread::query()->whereIn('id',$ids)->get();

        foreach ($thread as  $k => $v) {

            if($v->title == '' || empty($v->title)) {
                $threadTitle = '，其ID为'. $v->id;
            }else{
                $threadTitle = '【'. $v->title .'】';
            }
            //审核主题
            if (empty($v->deleted_at) && in_array($arr[$v->id]['isApproved'], [1, 2]) && $v->is_approved != $arr[$v->id]['isApproved']) {

                $v->is_approved = $arr[$v->id]['isApproved'];
                $v->save();

                if ($arr[$v->id]['isApproved'] == 1) {
                    $action_desc = $threadTitle.',通过审核';
                    //统计分类主题数+1
                    Category::refreshThreadCountV3($v->category_id);
                    //发送@用户消息
                    $threadIds[] = $v->id;
                } else {
                    $action_desc = $threadTitle.',被忽略';
                }


                $logArr[] = $this->logs('用户主题帖'. $action_desc);
                //删除主题
            }else if (in_array($arr[$v->id]['isDeleted'],[true, false])) {

                if ($arr[$v->id]['isDeleted'] == true) {
                    //软删除
                    if (empty($v->deleted_at)) {

                        $v->deleted_user_id = $user->id;
                        $v->deleted_at = Carbon::now();
                        $v->save();

                        // 通知
                        $this->threadNotices($v, $user, 'isDeleted', $arr[$v->id]['message'] ?? '');

                        // 日志
                        UserActionLogs::writeLog($user, $v, 'hide', $arr[$v->id]['message'] ?? '');

                        //统计分类主题数-1
                        Category::refreshThreadCountV3($v->category_id);

                        $logArr[] = $this->logs('软删除用户主题帖'. $threadTitle);

                        //真删除
                    } else if (!empty($v->deleted_at)) {

                        $deleteThreads[] = $v->id;

                        $logArr[] = $this->logs('真删除用户主题帖'. $threadTitle);

                    }

                }
                //还原被删除的主题
                if (!empty($v->deleted_at) && $arr[$v->id]['isDeleted'] == false) {

                    $v->deleted_user_id = null;
                    $v->deleted_at = null;
                    $v->save();

                    // 日志
                    UserActionLogs::writeLog($user, $v, 'restore', $arr[$v->id]['message'] ?? '');

                    //统计分类主题数+1
                    Category::refreshThreadCountV3($v->category_id);

                    $logArr[] = $this->logs('还原用户主题帖'. $threadTitle);
                }
            }
        }

        //处理真删除
        if (isset($deleteThreads)) {
            Thread::query()->whereIn('id',$deleteThreads)->delete();
        }

        if (isset($threadIds)) {
            $this->threadsSendMiddleware($threadIds);
        }

        CacheKey::delListCache();
        return $logArr;
    }

    public function posts()
    {

        $user = $this->user;
        $data = $this->inPut('data');
        $ids = array_column($data,'id');
        $arr = array_column($data,null,'id');

        $Post = Post::query()->whereIn('id',$ids)->get();

        $logArr = [];
        foreach( $Post as $k => $v ){

            if($v->content == '' || empty($v->content)) {
                $threadContent = '，其ID为'. $v->id;
            }else{
                $threadContent = '【'. $v->content .'】';
            }
            //审核回复
            if (empty($v->deleted_at) && in_array($arr[$v->id]['isApproved'], [1, 2]) && $v->is_approved != $arr[$v->id]['isApproved']) {

                $v->is_approved = $arr[$v->id]['isApproved'];
                $v->save();

                if ($arr[$v->id]['isApproved']==1) {
                    $action_desc = $threadContent.',通过审核';
                    //统计帖子评论数+1
                    $v->thread->refreshPostCount();
                    $v->thread->save();
                    //发送@用户短信信息
                    $this->postSendMiddleware($v);

                } else {
                    $action_desc = $threadContent.',被忽略';
                }

                $logArr[] = $this->logs('用户回复评论'. $action_desc);

                //删除回复
            } else if (in_array($arr[$v->id]['isDeleted'],[true, false])) {

                if ($arr[$v->id]['isDeleted'] == true) {

                    if (empty($v->deleted_at)) {

                        $v->deleted_user_id = $user->id;
                        $v->deleted_at = Carbon::now();
                        $v->save();

                        // 通知
                        $this->postNotices($v, $user, 'isDeleted', $arr[$v->id]['message'] ?? '');

                        // 日志
                        UserActionLogs::writeLog($user, $v, 'hide', $arr[$v->id]['message'] ?? '');

                        //统计帖子评论数-1
                        $v->thread->refreshPostCount();
                        $v->thread->save();

                        $logArr[] = $this->logs('软删除用户回复评论'. $threadContent);
                        //真删除
                    } else if (!empty($v->deleted_at)) {

                        $deletePosts[] = $v->id;

                        $logArr[] = $this->logs('真删除用户回复评论'. $threadContent);

                    }

                }
                //还原被删除回复
                if ( !empty($v->deleted_at) && $arr[$v->id]['isDeleted'] == false) {

                    $v->deleted_user_id = null;
                    $v->deleted_at = null;
                    $v->save();

                    // 日志
                    UserActionLogs::writeLog($user, $v, 'restore', $arr[$v->id]['message'] ?? '');

                    //统计帖子评论数+1
                    $v->thread->refreshPostCount();
                    $v->thread->save();

                    $logArr[] = $this->logs('还原用户回复评论'. $threadContent);

                }
            }
        }

        //处理真删除
        if (isset($deletePosts)) {
            Post::query()->whereIn('id',$deletePosts)->delete();
        }

        return $logArr;
    }

    //发帖发帖@用户判断处理
    public function threadsSendMiddleware($threadIds){
        $posts = Post::query()
            ->whereIn('thread_id',$threadIds)
            ->where('is_first',true)
            ->get();

        foreach ($posts as $key=>$post) {
            if (empty($post->parsedContent)) {
                continue;
            }

            $actor = User::query()->where('id',$post->user_id)->first();
            $newsNameArr = $this->sendContentHandle($post, $actor);
            if (empty($newsNameArr)){
                continue;
            }

            $users = User::query()->whereIn('username', $newsNameArr)->get();
            if (empty($users)) {
                continue;
            }
            $this->sendRelated($post, $actor, $users);
        }
    }

    //评论发帖@用户判断处理
    public function postSendMiddleware($post){
        if (empty($post->parsedContent)) {
            return;
        }

        $actor = User::query()->where('id',$post->user_id)->first();
        //领取红包
        $this->redPackets($post,$actor);
        $newsNameArr = $this->sendContentHandle($post, $actor);
        if (empty($newsNameArr)){
            return;
        }

        $users = User::query()->whereIn('username', $newsNameArr)->get();
        if (empty($users)) {
            return;
        }

        $this->sendRelated($post, $actor, $users);
    }

    //发送@内容处理
    private function sendContentHandle($post ,$actor){
        preg_match_all('/<span.*>(.*)<\/span>/isU', $post->parsedContent, $newsNameArr);

        $newsNameArr = $newsNameArr[1];
        if (empty($newsNameArr)) {
            return;
        }

        $newsNameArr2 = [];
        foreach ($newsNameArr as $v) {
            $string = trim(substr($v, 1));
            if ($actor->nickname != $string) {
                $newsNameArr2[] = $string;
            }
        }
        return $newsNameArr2;
    }

    //发帖@用户发送通知消息
    private function sendRelated($post, $actor, $users)
    {
        $post->mentionUsers()->sync(array_column($users->toArray(), 'id'));

        $users->load('deny');
        $users->filter(function ($user) use ($post) {
            //把作者拉黑的用户不发通知
            return !in_array($post->user_id, array_column($user->deny->toArray(), 'id'));
        })->each(function (User $user) use ($post, $actor) {
            // Tag 发送通知
            $user->notify(new Related($actor, $post));
        });
    }

    public function logs($actionDesc){
        return [
            'user_id' => $this->user->id,
            'action_desc' => $actionDesc,
            'ip' => ip($this->request->getServerParams()),
            'created_at' => Carbon::now()
        ];
    }
    //审核领取红包
    public function redPackets($post,$actor){
        $this->events = app()->make(Dispatcher::class);
        $data = [
            'type' => 'posts',
            'relationships' => [
                'thread' => [
                    'data' => [
                        'type' => 'threads',
                        'id' => $post->thread_id
                    ]
                ]
            ],
            'attributes' => [
                'content' => $post->parsedContent,
                'isComment' => $post->is_comment,
                'replyId' => $post->id,
                'replyUserId' => $post->reply_post_id,
                'commentUserId' => $post->comment_user_id
            ]
        ];
        $post->raise(new Saved($post, $actor, $data));

        // TODO: 通知相关用户，在给定的整个持续时间内，每位用户只能收到一个通知
        // $this->notifications->onePerUser(function () use ($post, $actor) {
        $this->dispatchEventsFor($post, $actor);
    }

}
