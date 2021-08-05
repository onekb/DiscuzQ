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

namespace App\Api\Controller\ThreadsV3;

use App\Common\CacheKey;
use App\Common\ResponseCode;
use App\Models\Category;
use App\Models\Group;
use App\Models\Order;
use App\Models\OrderChildren;
use App\Models\Post;
use App\Models\Thread;
use App\Models\ThreadRedPacket;
use App\Models\ThreadReward;
use App\Models\ThreadTag;
use App\Models\ThreadTom;
use App\Models\User;
use App\Modules\ThreadTom\TomConfig;
use App\Notifications\Messages\Database\PostMessage;
use App\Notifications\System;
use App\Repositories\UserRepository;
use Discuz\Base\DzqCache;
use Discuz\Base\DzqController;
use Illuminate\Support\Arr;

class UpdateThreadController extends DzqController
{
    use ThreadTrait;

    private $thread;

    protected function checkRequestPermissions(UserRepository $userRepo)
    {
        $this->thread = $this->thread = Thread::query()
            ->where(['id' => $this->inPut('threadId')])
            ->whereNull('deleted_at')
            ->first();
        if (!$this->thread) {
            $this->outPut(ResponseCode::RESOURCE_NOT_FOUND, '帖子不存在');
        }
        //编辑前验证手机，验证码，实名
        $this->userVerify($this->user);
        return $userRepo->canEditThread($this->user, $this->thread);
    }

    public function main()
    {
        $threadId = $this->inPut('threadId');
        $thread = $this->thread;
        $post = Post::query()
            ->where(['thread_id' => $threadId, 'is_first' => Post::FIRST_YES])
            ->whereNull('deleted_at')
            ->first();
        if (empty($post)) {
            $this->outPut(ResponseCode::RESOURCE_NOT_FOUND, '帖子详情不存在');
        }
        $oldContent = $post->content;
        $result = $this->updateThread($thread, $post);

        if (
            ($thread->user_id != $this->user->id)
            && ($oldContent != $post->content)
            && $thread->user
        ) {
            $thread->user->notify(new System(PostMessage::class, $this->user, [
                'message' => $oldContent,
                'post' => $post,
                'notify_type' => Post::NOTIFY_EDIT_CONTENT_TYPE,
            ]));
        }

        $this->outPut(ResponseCode::SUCCESS, '', $result);
    }

    private function updateThread($thread, $post)
    {
        $db = $this->getDB();
        $db->beginTransaction();
        try {
            $result = $this->executeEloquent($thread, $post);
            $db->commit();
            return $result;
        } catch (\Exception $e) {
            $db->rollBack();
            $this->info('updateThread_error_' . $this->user->id, $e->getMessage());
            $this->outPut(ResponseCode::DB_ERROR, $e->getMessage());
        }
    }

    private function executeEloquent($thread, $post)
    {
        $content = $this->inPut('content');//非必填项

        if (!empty($content['text'])) {
            $content['text'] = $this->optimizeEmoji($content['text']);
            //处理@
            $content['text'] = $this->renderCall($content['text']);
        }

        //更新thread数据
        $this->saveThread($thread, $content);
        //插入话题
        $content = $this->saveTopic($thread, $content);
        //更新post数据
        $this->savePost($post, $content);
        //发帖@用户
        $this->sendNews($thread, $post);
        //更新tom数据
        $tomJsons = $this->saveThreadTom($thread, $content, $post);
        return $this->getResult($thread, $post, $tomJsons);
    }


    private function saveThread($thread, &$content)
    {
        $title = $this->inPut('title');//非必填项
        $categoryId = $this->inPut('categoryId');
        $price = floatval($this->inPut('price'));
        $attachmentPrice = floatval($this->inPut('attachmentPrice'));
        $freeWords = floatval($this->inPut('freeWords'));
        $position = $this->inPut('position');
        $isAnonymous = $this->inPut('anonymous');
        $isDraft = $this->inPut('draft');
        //如果原帖是已发布的情况下，update 不允许在将帖子状态存为草稿
        if($thread->is_draft == Thread::IS_NOT_DRAFT && !empty($isDraft)){
            $this->outPut(ResponseCode::INVALID_PARAMETER, '该帖已发布，不允许再存为草稿');
        }

        // 包含 红包 的帖子在已发布的情况下，再次编辑；条件：存在对应的 order 为 已支付的情况
        empty($content['indexes']) && $content['indexes'] = [];
        if ($this->needPay($content['indexes']) && $isDraft == Thread::IS_NOT_DRAFT) {
            $order = $this->getOrderInfo($thread);
            if($order){
                if($order->status != Order::ORDER_STATUS_PAID){
                    $this->outPut(ResponseCode::INVALID_PARAMETER, '订单未支付，无法发布');
                }
            }else{
                // 已发布的没有 “红包/悬赏” 的帖子，不允许重新编辑时，增加 “红包/悬赏” 属性
                if($thread->is_draft == 0){
                    $tags = ThreadTag::query()->where('thread_id', $thread->id)->pluck('tag')->toArray();
                    $intersect_tags = array_intersect([ThreadTag::RED_PACKET, ThreadTag::REWARD], $tags);
                    if(empty($intersect_tags)){
                        $this->outPut(ResponseCode::INVALID_PARAMETER, '已发布的帖子，不允许添加 红包/悬赏');
                    }
                }
                $this->outPut(ResponseCode::INVALID_PARAMETER, '包含红包/悬赏帖，需要创建对应的订单');
            }

        }


        $thread->title = $title;
        !empty($categoryId) && $thread->category_id = $categoryId;
        if (!empty($position)) {
            $thread->longitude = $position['longitude'] ?? 0;
            $thread->latitude = $position['latitude'] ?? 0;
            $thread->address = $position['address'] ?? '';
            $thread->location = $position['location'] ?? '';
        }

        $thread->price = $price > 0 ? ($price) : 0;
        $thread->attachment_price = $attachmentPrice > 0 ? $attachmentPrice : 0;
        $thread->free_words = $freeWords > 0 ? $freeWords : 0;

        [$newTitle, $newContent] = $this->boolApproved($title, $content['text'], $isApproved);
        $content['text'] = $newContent;
        !empty($title) && $thread->title = $newTitle;
        if ($isApproved) {
            $thread->is_approved = Thread::BOOL_NO;
        } else {
            $thread->is_approved = Thread::BOOL_YES;
        }

        if ($isDraft) {
            $thread->is_draft = Thread::IS_DRAFT;
        } else {
            if ($thread->is_draft) {
                $thread->created_at = date('Y-m-d H:i:m', time());
            }
            $thread->is_draft = Thread::IS_NOT_DRAFT;
        }
        if($isAnonymous){
            $thread->is_anonymous = Thread::BOOL_YES;
        }else{
            $thread->is_anonymous = Thread::BOOL_NO;
        }


        $thread->save();
        if (!$isApproved && !$isDraft) {
            $this->user->refreshThreadCount();
            $this->user->save();
            Category::refreshThreadCountV3($categoryId);
        }
    }

    private function savePost($post, $content)
    {
        [$ip, $port] = $this->getIpPort();
        if (isset($content['text'])) {
            $post->content = $content['text'];
        }
        $post->ip = $ip;
        $post->port = $port;
        $post->is_first = Post::FIRST_YES;
        $post->is_approved = Post::APPROVED;
        $post->save();
    }

    private function saveThreadTom($thread, $content, $post)
    {
        $threadId = $thread->id;
        $tags = [];
        /* 允许红包帖在已发布情况下再次编辑，相当于允许 包含 红包 的帖子，draft 为 0
        if(!empty($content['indexes'])){
            //针对红包帖、悬赏帖，还需要往对应的 body 中插入  draft = 1
            $tomTypes = array_keys($content['indexes']);
            foreach ($tomTypes as $tomType) {
                $tomService = Arr::get(TomConfig::$map, $tomType.'.service');
                if(constant($tomService.'::NEED_PAY')){
                    if($this->inPut('draft') == 0){        //如果修改帖子的时候，增加了红包资料的话，那么必须要先走草稿，然后走订单，再走发布（或者简单点理解为：增加了新的红包的帖子状态，只能通过支付回调来修改帖子状态）
                        $this->outPut(ResponseCode::INVALID_PARAMETER, '红包/悬赏红包应先存为草稿');
                    }
                    if ($content['indexes'][$tomType]['body']['draft'] == 0 ) {
                        $this->outPut(ResponseCode::INVALID_PARAMETER, '红包/悬赏红包状态应为草稿');
                    }
                }
            }
        }
        */
        $order = $this->getOrderInfo($thread);
        $tomJsons = $this->tomDispatcher($content, null, $thread->id, $post->id);
        if (!empty($content['text'])) {
            $tags[] = ['thread_id' => $thread['id'], 'tag' => TomConfig::TOM_TEXT];
        }
        foreach ($tomJsons as $key => $value) {
            $tomId = $value['tomId'];
            $operation = $value['operation'];
            $body = $value['body'];
            $operation != $this->DELETE_FUNC && $tags[] = ['thread_id' => $threadId, 'tag' => $value['tomId']];
            switch ($operation) {
                case $this->CREATE_FUNC:
                    ThreadTom::query()->insert([
                        'thread_id' => $threadId,
                        'tom_type' => $tomId,
                        'key' => $key,
                        'value' => json_encode($body, 256),
                        'status' => ThreadTom::STATUS_ACTIVE
                    ]);
                    break;
                case $this->DELETE_FUNC:
                    ThreadTom::query()
                        ->where(['thread_id' => $threadId, 'tom_type' => $tomId, 'status' => ThreadTom::STATUS_ACTIVE])
                        ->update(['status' => ThreadTom::STATUS_DELETE]);
                    $isDeleteRedOrder = $isDeleteRewardOrder = false;
                    if(empty($order) || $order->status != Order::ORDER_STATUS_PAID){
                        if($tomId == TomConfig::TOM_REDPACK)        $isDeleteRedOrder = true;
                        if($tomId == TomConfig::TOM_REWARD)        $isDeleteRewardOrder = true;
                    }
                    $this->delRedRelations($threadId, $isDeleteRedOrder, $isDeleteRewardOrder);
                    break;
                case $this->UPDATE_FUNC:
                    ThreadTom::query()
                        ->where(['thread_id' => $threadId, 'tom_type' => $tomId, 'key' => $key, 'status' => ThreadTom::STATUS_ACTIVE])
                        ->update(['value' => json_encode($body, 256)]);
                    break;
                default:
                    $this->outPut(ResponseCode::UNKNOWN_ERROR, 'operation ' . $operation . ' not exist.');
            }
        }
        $this->delRedundancyPlugins($threadId, $tomJsons);
        $this->saveThreadTag($threadId, $tags);
        return $tomJsons;
    }

    //删除掉前端未提交的的插件和标签数据
    private function delRedundancyPlugins($threadId, $tomJsons)
    {
        $order = $this->getRedOrderInfo($threadId);
        $tomList = ThreadTom::query()
            ->select('tom_type', 'key')
            ->where(['thread_id' => $threadId, 'status' => ThreadTom::STATUS_ACTIVE])->get();
        $keys = [];
        $isDeleteRedOrder = $isDeleteRewardOrder = false;
        foreach ($tomList as $item) {
            if (empty($tomJsons[$item['key']])) {
                if (in_array($item['tom_type'], [TomConfig::TOM_REDPACK, TomConfig::TOM_REWARD]) &&
                    (empty($order) || $order->status != Order::ORDER_STATUS_PAID)
                ) {
                    if ($item['tom_type'] == TomConfig::TOM_REDPACK) $isDeleteRedOrder = true;
                    if ($item['tom_type'] == TomConfig::TOM_REWARD) $isDeleteRewardOrder = true;
                }
                $keys[] = $item['key'];
            }
        }

        ThreadTom::query()
            ->select('tom_type', 'key')
            ->where(['thread_id' => $threadId])
            ->whereIn('key', $keys)->delete();

        $this->delRedRelations($threadId, $isDeleteRedOrder, $isDeleteRewardOrder);
    }

    private function saveThreadTag($threadId, $tags)
    {
        ThreadTag::query()->where('thread_id', $threadId)->delete();
        ThreadTag::query()->insert($tags);
    }

    private function getResult($thread, $post, $tomJsons)
    {
        $user = User::query()->where('id', $thread->user_id)->first();
        $group = Group::getGroup($user->id);
        return $this->packThreadDetail($user, $group, $thread, $post, $tomJsons, true);
    }

    public function prefixClearCache($user)
    {
        CacheKey::delListCache();
        $threadId = $this->inPut('threadId');
        DzqCache::delHashKey(CacheKey::LIST_THREADS_V3_THREADS, $threadId);
        DzqCache::delHashKey(CacheKey::LIST_THREADS_V3_POSTS, $threadId);
        DzqCache::delHashKey(CacheKey::LIST_THREADS_V3_TAGS, $threadId);
        DzqCache::delHashKey(CacheKey::LIST_THREADS_V3_TOMS, $threadId);
    }
}

