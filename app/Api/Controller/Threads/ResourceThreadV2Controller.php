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
use App\Formatter\Formatter;
use App\Models\Attachment;
use App\Models\Order;
use App\Models\Post;
use App\Models\PostGoods;
use App\Models\PostUser;
use App\Models\Question;
use App\Models\RedPacket;
use App\Models\Thread;
use App\Models\ThreadReward;
use App\Models\ThreadUser;
use App\Models\ThreadVideo;
use App\Repositories\UserFollowRepository;
use App\Settings\SettingsRepository;
use Carbon\Carbon;
use Discuz\Base\DzqController;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\Str;
use s9e\TextFormatter\Utils;

class ResourceThreadV2Controller extends DzqController
{
    public $userFollow;
    public $settings;

    //返回的数据一定包含的数据
    public $include = [
        'firstPost',
        'firstPost.likedUsers:id,username,avatar',
        'firstPost.mentionUsers:id,username,avatar',
        'user:id,username,avatar,follow_count as followCount,updated_at as updatedAt',
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


    public function __construct(UserFollowRepository $userFollow, SettingsRepository $settings)
    {
        $this->userFollow = $userFollow;
        $this->settings = $settings;
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
                $data[$key] = $thread->$key->map(function ($item){
                    $item->avatarUrl = $item->avatar;
                    return $item->only(['id','username','avatarUrl']);
                });
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

        $this->parseContent($thread->firstPost, $this->request);

        $data['firstPost'] = $post_serialize->getDefaultAttributes($thread->firstPost);
        //为了前端编辑帖子，这里重写了 content
        $data['firstPost']['parseContentHtml'] = !empty($thread->firstPost->parseContentHtml) ? $thread->firstPost->parseContentHtml : $data['firstPost']['content'];
        $data['firstPost']['canLike'] = (bool) $this->user->can('like', $thread->firstPost);
        if ($likeState = $thread->firstPost->likeState) {
            $data['firstPost']['isLiked'] = true;
            $data['firstPost']['likedAt'] = $this->formatDate($likeState->created_at);
        } else {
            $data['firstPost']['isLiked'] = false;
        }

        if($thread->firstPost->likedUsers){
            $data['likedUsers'] = $thread->firstPost->likedUsers->map(function ($item){
                    $item->avatarUrl = $item->avatar;
                    return $item->only(['id','username','avatarUrl','pivot']);
                });
        }else{
            $data['likedUsers'] = [];
        }
        $data['mentionUsers'] = $thread->firstPost->mentionUsers ?? [];
        $data['images'] = [];
        $urlKey = ''; $urlExpire = 0;
        if(in_array($thread->type, [Thread::TYPE_OF_VIDEO, Thread::TYPE_OF_AUDIO])){
            $urlKey = $this->settings->get('qcloud_vod_url_key', 'qcloud');
            $urlExpire = (int) $this->settings->get('qcloud_vod_url_expire', 'qcloud');
        }
        switch ($thread->type){
            case Thread::TYPE_OF_VIDEO:
                $threadVideo = ThreadVideo::query()->where(['thread_id' => $thread->id, 'status' => 1, 'type' => 0])->first();
                if ($threadVideo && isset($threadVideo->media_url)) {
                    $threadVideo->mediaUrl = $threadVideo->media_url;
                } else {
                    $threadVideo = [];
                }
                if(empty($threadVideo)){        //如果没有转码成功的就去最后一个草稿
                    $threadVideo = ThreadVideo::query()->where(['thread_id' => $thread->id, 'status' => 0])->orderBy('id', 'desc')->first();
                }
                $thread->threadVideo = $threadVideo;
                if ($urlKey && $urlExpire && $thread->threadVideo->mediaUrl) {
                    $currentTime = Carbon::now()->timestamp;
                    $dir = Str::beforeLast(parse_url($thread->threadVideo->mediaUrl)['path'], '/') . '/';
                    $t = dechex($currentTime+$urlExpire);
                    $us = Str::random(10);
                    $sign = md5($urlKey . $dir . $t . $us);
                    $thread->threadVideo->mediaUrl = $thread->threadVideo->mediaUrl . '?t=' . $t . '&us='. $us . '&sign='.$sign;
                }
                $data['threadVideo'] = $thread->threadVideo ?? [];
                break;
            case Thread::TYPE_OF_AUDIO:
                $threadAudio = ThreadVideo::query()->where(['thread_id' => $thread->id, 'status' => 1, 'type' => 1])->first();
                if ($threadAudio && isset($threadAudio->media_url)) {
                    $threadAudio->mediaUrl = $threadAudio->media_url;
                } else {
                    $threadAudio = [];
                }
                if(empty($threadAudio)){        //如果没有转码成功的就去最后一个草稿
                    $threadAudio = ThreadVideo::query()->where(['thread_id' => $thread->id, 'status' => 0, 'type' => 1])->orderBy('id', 'desc')->first();
                }
                $thread->threadAudio = $threadAudio;
                if ($urlKey && $urlExpire && $thread->threadAudio->mediaUrl) {
                    $currentTime = Carbon::now()->timestamp;
                    $dir = Str::beforeLast(parse_url($thread->threadAudio->mediaUrl)['path'], '/') . '/';
                    $t = dechex($currentTime+$urlExpire);
                    $us = Str::random(10);
                    $sign = md5($urlKey . $dir . $t . $us);
                    $thread->threadAudio->mediaUrl = $thread->threadAudio->mediaUrl . '?t=' . $t . '&us='. $us . '&sign='.$sign;
                }
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
        $data['canFavorite'] = (bool) $this->user->can('favorite', $thread);

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


        $thread->timestamps = false;
        if(!$stopViewCount)     $thread->increment('view_count');


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


    /**
     * 渲染content 内容
     */
    public function parseContent($post, $request){
        // 图文混排需要替换插入文中的图片及附件地址

        if(!($post->is_first && $post->thread->type === Thread::TYPE_OF_LONG)){
            return '';
        }


        /** @var AttachmentSerializer $attachmentSerializer */
        $attachmentSerializer = app(AttachmentSerializer::class);

        $attachmentSerializer->setRequest($request);

        // 所有图片及附件 URL
        $attachments = $post->images
            ->merge($post->attachments)
            ->keyBy('id')
            ->map(function (Attachment $attachment) use ($attachmentSerializer) {
                if ($attachment->type === Attachment::TYPE_OF_IMAGE) {
                    return $attachmentSerializer->getDefaultAttributes($attachment)['url'];
                } elseif ($attachment->type === Attachment::TYPE_OF_FILE) {
                    return $attachmentSerializer->getDefaultAttributes($attachment)['url'];
                }
            });

        // 数据原始内容，即 s9e 解析后的 XML
        $xml = $post->getRawOriginal('content');

        // 替换插入内容中的图片 URL
        $xml = Utils::replaceAttributes($xml, 'IMG', function ($attributes) use ($attachments) {
            if (isset($attributes['title']) && isset($attachments[$attributes['title']])) {
                $attributes['src'] = $attachments[$attributes['title']];
            }

            return $attributes;
        });

        // 替换插入内容中的附件 URL
        $xml = Utils::replaceAttributes($xml, 'URL', function ($attributes) use ($attachments) {
            if (isset($attributes['title']) && isset($attachments[$attributes['title']])) {
                $attributes['url'] = $attachments[$attributes['title']];
            }

            return $attributes;
        });

        $will_parse_content = $post->content;
        $post->parseContentHtml = $will_parse_content;
        if(!empty($post->content) && !empty($attachments)){
            $post->parseContentHtml = preg_replace_callback(
                '((!\[[^\]]*\])(\((https[^\)]*) ("\d+")\)))',
                function($m) use ($attachments){
                    if(!empty($m)){
                        $id = trim($m[4], '"');
                        return $m[1].'('.$attachments[$id].' '.$m[4].')';
                    }
                },
                $will_parse_content
            );
        }


        $post->parsedContent = $xml;
    }




}
