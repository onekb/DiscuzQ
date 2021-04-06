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

namespace App\Api\Controller\Threads;


use App\Api\Serializer\AttachmentSerializer;
use App\Api\Serializer\CategorySerializer;
use App\Api\Serializer\PostGoodsSerializer;
use App\Api\Serializer\PostSerializer;
use App\Api\Serializer\QuestionAnswerSerializer;
use App\Api\Serializer\ThreadRewardSerializer;
use App\Api\Serializer\ThreadSerializer;
use App\Common\CacheKey;
use App\Common\ResponseCode;
use App\Models\Order;
use App\Models\PostGoods;
use App\Models\PostUser;
use App\Models\Question;
use App\Models\RedPacket;
use App\Models\Thread;
use App\Models\ThreadReward;
use App\Models\ThreadUser;
use App\Repositories\UserFollowRepository;
use Discuz\Base\DzqController;

class ResourceThreadV2Controller extends DzqController
{
    public $userFollow;


    //返回的数据一定包含的数据
    public $include = [
        'firstPost',
        'firstPost.likedUsers:id,username,avatar',
        'firstPost.mentionUsers:id,username,avatar',
        'user:id,username,avatar,follow_count as followCount',
        'user.groups:id,name,is_display as isDisplay',
        'category:id,name,description'
    ];


    //针对不同类型的贴，关联不同
    public $switch_include = [
        Thread::TYPE_OF_VIDEO  =>  ['threadVideo:id,thread_id,type,file_name as fileName,file_id as fileId,media_url as mediaUrl,cover_url as coverUrl,status,duration,width,height'],
        Thread::TYPE_OF_AUDIO  =>  ['threadAudio:id,thread_id,type,file_name as fileName,file_id as fileId,media_url as mediaUrl,cover_url as coverUrl,status,duration,width,height'],
        Thread::TYPE_OF_GOODS  =>  ['firstPost.postGoods'],
        Thread::TYPE_OF_QUESTION   =>  []
    ];

    //特殊关联
    public $relation = [
        'rewardedUsers' => Order::ORDER_TYPE_REWARD,            //打赏的人
        'paidUsers' => Order::ORDER_TYPE_THREAD,                //付费用户
        'onlookers' => Order::ORDER_TYPE_ONLOOKER                 //围观用户
    ];

    public function __construct(UserFollowRepository $userFollow)
    {
        $this->userFollow = $userFollow;
    }

    public function main()
    {
        $thread_serialize = $this->app->make(ThreadSerializer::class);
		$thread_serialize->setRequest($this->request);
        $post_serialize = $this->app->make(PostSerializer::class);
		$post_serialize->setRequest($this->request);
        $attachment_serialize = $this->app->make(AttachmentSerializer::class);
		$attachment_serialize->setRequest($this->request);
        $category_serialize = $this->app->make(CategorySerializer::class);
        $category_serialize->setRequest($this->request);


        $thread_id = $this->inPut('pid');
        $stopViewCount = $this->inPut('stopViewCount');
        if(empty($thread_id))       return  $this->outPut(ResponseCode::INVALID_PARAMETER );
        $thread = Thread::find($thread_id);
        if(empty($thread))          return  $this->outPut(ResponseCode::NET_ERROR);
        $include = !empty($this->inPut('include')) ? array_unique(array_merge($this->include, explode(',', $this->inPut('include')))) : $this->include;

        $data = [];
        /*  暂时不需要缓存，要用缓存的时候注释打开和下面的put注释打开即可
                $cacheKey = CacheKey::THREAD_RESOURCE_BY_ID.$thread_id;
                $cache = app('cache');
                $cacheData = $cache->get($cacheKey);
                if(!empty($cacheData)){
                    $cacheThread = unserialize($cacheData);
                    $cache_thread_id = $cacheThread['thread']['id'];
                    $cache_thread = Thread::find($cache_thread_id);
                    if(!$stopViewCount){
                        $cache_thread->increment('view_count');
                        $cacheThread['thread']['viewCount'] ++;
                    }
                    return $this->outPut(ResponseCode::SUCCESS,'', $cacheThread);
                }
        */
        if(in_array($thread->type, array_keys($this->switch_include))){
            $include = array_merge($include, $this->switch_include[$thread->type]);
        }
        foreach ($this->relation as $key => $val){
            $this->loadOrderUsers($thread, $val);
            if(empty($thread->$key)){
                $data[$key] = [];
            }else{
                $data[$key] = $thread->$key;
            }
        }
        $thread->loadMissing($include);

        $data['thread'] = $thread_serialize->getDefaultAttributes($thread);
        $data['category'] = $category_serialize->getDefaultAttributes($thread->category);

        $data['author'] = $thread->user && $thread->user->groups->makeHidden('pivot') ? $thread->user->toArray()  : [];
        $data['author']['follow'] = $this->userFollow->findFollowDetail($this->user->id, $thread->user->id);
        $data['author']['isReal'] = isset($thread->user->realname) && $thread->user->realname != null ? true : false;
        $data['author']['groups'] = $thread->user->groups->map(function ($item){
            $item->isDisplay = boolval($item->isDisplay);
            return $item->only(['id','name','isDisplay']);
        });


        $data['firstPost'] = $post_serialize->getDefaultAttributes($thread->firstPost);
        $data['firstPost']['canLike'] = (bool) $this->user->can('like', $thread->firstPost);
        if ($likeState = $thread->firstPost->likeState) {
            $data['firstPost']['isLiked'] = true;
            $data['firstPost']['likedAt'] = $this->formatDate($likeState->created_at);
        } else {
            $data['firstPost']['isLiked'] = false;
        }

        $data['likedUsers'] = $thread->firstPost->likedUsers ?? [];
        $data['mentionUsers'] = $thread->firstPost->mentionUsers ?? [];
        $data['images'] = [];
        switch ($thread->type){
            case Thread::TYPE_OF_VIDEO:
                $data['threadVideo'] = $thread->threadVideo ?? [];
                break;
            case Thread::TYPE_OF_AUDIO:
                $data['threadAudio'] = $thread->threadAudio ?? [];
                break;
            case Thread::TYPE_OF_GOODS:
                $data['postGoods'] = PostGoods::instance()->getPostGoods($thread->firstPost->id);
                break;
            case Thread::TYPE_OF_QUESTION:
                $questionType = ThreadReward::query()->where('thread_id', $thread_id)->first();
                if(isset($questionType['type']) && $questionType['type'] == 0) {
                    $question_serialize = $this->app->make(ThreadRewardSerializer::class);
                } else{
                    $question_serialize = $this->app->make(QuestionAnswerSerializer::class);
                }
                $question_serialize->setRequest($this->request);
                $data['question'] = $question_serialize->getDefaultAttributes($thread->question);
                $data['question']['beUser'] = $thread->question->beUser ? $thread->question->beUser->only(['id','username','avatar','groups']) : [];
                if(!empty($data['question']['beUser'])){
                    $data['question']['beUser']['isReal'] = isset($thread->question->beUser->realname) && $thread->question->beUser->realname != null ? true : false;
                    $data['question']['beUser']['groups'] = $thread->question->beUser->groups->map(function ($item){
                        return  $item->only(['id', 'name']);
                    });
                }

                $data['question']['images'] = [];
                if(!empty($thread->question->images)){
                    foreach ($thread->question->images as $val){
                        $data['question']['images'][] = $attachment_serialize->getDefaultAttributes($val);
                    }
                }
                break;

        }

        if(!empty($thread->firstPost->images)){
            foreach ($thread->firstPost->images as $val){
                $data['images'][] = $attachment_serialize->getDefaultAttributes($val);
            }
        }
        $data['attachments'] = [];
        if(!empty($thread->firstPost->attachments)){
            foreach ($thread->firstPost->attachments as $val){
                $data['attachments'][] = $attachment_serialize->getDefaultAttributes($val);
            }
        }



        // 问答贴设置当前用户
        /*
        if($thread->type == Thread::TYPE_OF_QUESTION){
            $data['thread']['question']['thread'] = $data['thread'];
        }
        */

        if($thread->is_red_packet){
            $redPacket = RedPacket::query()->where('thread_id', $thread_id)->first();
            $data['redPacket'] = $redPacket ? $redPacket->toArray() : [];
        }
        $data['canFavorite'] = (bool) $this->user->can('favorite', $thread->firstPost);

        if ($favoriteState = $thread->favoriteState) {
            $data['isFavorite'] = true;
            $data['favoriteAt'] = $this->formatDate($favoriteState->created_at);
        } else {
            $data['isFavorite'] = false;
        }


        $isLiked = PostUser::query()->where(['post_id' => $thread->firstPost->id, 'user_id' => $this->user->id ])->exists();
        if($isLiked){
            $data['isLiked'] = true;
        }else{
            $data['isLiked'] = false;
        }
        $data['paid'] = !empty($thread->price) ? true : false;
        $data['isPaidAttachment'] = !empty($thread->attachment_price) ? true : false;


        if(!$stopViewCount)     $thread->increment('view_count');

//        $cache->put($cacheKey, serialize($data), 5*60);
        $data = $this->camelData($data);

        //为了兼容前端
        if(empty($data['thread']['questionTypeAndMoney']))  $data['thread']['questionTypeAndMoney'] = ['type' => 1];
        return $this->outPut(ResponseCode::SUCCESS,'', $data);

    }


    /**
     * @param Thread $thread
     * @param int $type
     * @return Thread
     */
    private function loadOrderUsers(Thread $thread, $type)
    {
        switch ($type) {
            case Order::ORDER_TYPE_REWARD:
                $relation = 'rewardedUsers';
                break;
            case Order::ORDER_TYPE_THREAD:
                $relation = 'paidUsers';
                $type = [Order::ORDER_TYPE_THREAD, Order::ORDER_TYPE_ATTACHMENT];
                break;
            case Order::ORDER_TYPE_ONLOOKER:
                $relation = 'onlookers';
                break;
            default:
                return $thread;
        }

        $orderUsers = Order::with(['user' => function($query){
            $query->select('id','username','avatar');
        }])->where('thread_id', $thread->id)
            ->where('status', Order::ORDER_STATUS_PAID)
            ->whereIn('type', is_array($type) ? $type : [$type])
            ->where('is_anonymous', false)
            ->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc')
            ->get();

        return $thread->setRelation($relation, $orderUsers->pluck('user')->filter());
    }




}
