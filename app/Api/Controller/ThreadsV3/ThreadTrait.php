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

use App\Censor\Censor;
use App\Common\CacheKey;
use App\Common\ResponseCode;
use App\Traits\PostNoticesTrait;
use Discuz\Base\DzqCache;
use App\Formatter\Formatter;
use App\Notifications\Related;
use App\Models\Category;
use App\Models\MobileCode;
use App\Models\Order;
use App\Models\Post;
use App\Models\PostUser;
use App\Models\Thread;
use App\Models\ThreadTopic;
use App\Models\ThreadUser;
use App\Models\Topic;
use App\Models\User;
use App\Modules\ThreadTom\TomConfig;
use App\Modules\ThreadTom\TomTrait;
use App\Repositories\MobileCodeRepository;
use App\Repositories\UserRepository;
use App\Settings\SettingsRepository;
use App\SmsMessages\SendCodeMessage;
use Discuz\Qcloud\QcloudTrait;
use Illuminate\Support\Arr;
use App\Common\Utils;

trait ThreadTrait
{
    use TomTrait;
    use QcloudTrait;
    use PostNoticesTrait;

    private $loginUserData = [];

    public function packThreadDetail($user, $group, $thread, $post, $tomInputIndexes, $analysis = false, $tags = [], $loginUserData = [])
    {
        $loginUser = $this->user;
        $this->loginUserData = $loginUserData;
        $userField = $this->getUserInfoField($loginUser, $user, $thread);
        $groupField = $this->getGroupInfoField($loginUser, $group, $thread);
        $likeRewardField = $this->getLikeRewardField($thread, $post);//列表页传参
        $payType = $this->threadPayStatus($loginUser, $thread, $paid);
        $canViewTom = $this->canViewTom($loginUser, $thread, $payType, $paid);
        $canFreeViewTom = $this->canFreeViewTom($loginUser, $thread);
        $contentField = $this->getContentField($loginUser, $thread, $post, $tomInputIndexes, $payType, $paid, $canViewTom, $canFreeViewTom);
        $result = [
            'threadId' => $thread['id'],
            'postId' => $post['id'],
            'userId' => $thread['user_id'],
            'categoryId' => $thread['category_id'],
            'parentCategoryId' => $this->getParentCategory($thread['category_id'])['parentCategoryId'],
            'topicId' => $thread['topic_id'] ?? 0,
            'categoryName' => $this->getCategoryNameField($thread['category_id']),
            'parentCategoryName' => $this->getParentCategory($thread['category_id'])['parentCategoryName'],
            'title' => $thread['title'],
            'viewCount' => empty($thread['view_count']) ? 0 : $thread['view_count'],
            'isApproved' => $thread['is_approved'],
            'isStick' => $thread['is_sticky'],
            'isDraft' => boolval($thread['is_draft']),
            'isAnonymous' => $thread['is_anonymous'],
            'isFavorite' => $this->getFavoriteField($thread['id'], $loginUser),
            'price' => floatval($thread['price']),
            'attachmentPrice' => floatval($thread['attachment_price']),
            'payType' => $payType,
            'paid' => $paid,
            'isLike' => $this->isLike($loginUser, $post),
            'isReward' => $this->isReward($loginUser, $thread),
            'createdAt' => date('Y-m-d H:i:s', strtotime($thread['created_at'])),
            'updatedAt' => date('Y-m-d H:i:s', strtotime($thread['updated_at'])),
            'diffTime' => Utils::diffTime($thread['created_at']),
            'user' => $userField,
            'group' => $groupField,
            'likeReward' => $likeRewardField,
            'displayTag' => $this->getDisplayTagField($thread, $tags),
            'position' => [
                'longitude' => $thread['longitude'],
                'latitude' => $thread['latitude'],
                'address' => $thread['address'],
                'location' => $thread['location']
            ],
            'ability' => $this->getAbilityField($loginUser, $thread),
            'content' => $contentField,
            'freewords' => $thread['free_words']
        ];
        if ($analysis) {
            $concatString = $thread['title'] . $post['content'];
            list($searches, $replaces) = ThreadHelper::getThreadSearchReplace($concatString);
            $result['title'] = str_replace($searches, $replaces, $result['title']);
            $result['content']['text'] = str_replace($searches, $replaces, $result['content']['text']);
        }
        return $result;
    }

    public function userVerify($user)
    {
        $settingRepo = app(SettingsRepository::class);
        $mobileCodeRepo = app(MobileCodeRepository::class);
        if ((bool)$settingRepo->get('qcloud_sms')) {
            $realMobile = $user->getRawOriginal('mobile');
            if (empty($realMobile)) {
                $this->outPut(ResponseCode::USER_MOBILE_NOT_ALLOW_NULL);
            }
            //校验手机号和验证码
            $type = "thread_verify";
            $ip = ip($this->request->getServerParams());
            $mobileCode = $mobileCodeRepo->getSmsCode($realMobile, $type);
            if (!is_null($mobileCode) && $mobileCode->exists) {
                $mobileCode = $mobileCode->refrecode(MobileCode::CODE_EXCEPTION, $ip);
            } else {
                $mobileCode = MobileCode::make($realMobile, MobileCode::CODE_EXCEPTION, $type, $ip);
            }
            $result = $this->smsSend($realMobile, new SendCodeMessage(
                [
                    'code' => $mobileCode->code,
                    'expire' => MobileCode::CODE_EXCEPTION]
            ));
            if (!(isset($result['qcloud']['status']) && $result['qcloud']['status'] === 'success')) {
                $this->outPut(ResponseCode::SMS_CODE_ERROR);
            }
            $mobileCode->save();
        }
        if ((bool)$settingRepo->get('qcloud_faceid')) {
            $realName = $user->getRawOriginal('realname');
            $identity = $user->getRawOriginal('identity');
            if (empty($realName)) {
                $this->outPut(ResponseCode::REALNAME_NOT_NULL);
            }
            if (empty($identity)) {
                $this->outPut(ResponseCode::IDENTITY_NOT_NULL);
            }
            //检验身份证号码和姓名是否真实
            $qcloud = $this->app->make('qcloud');
            $res = $qcloud->service('faceid')->idCardVerification($identity, $realName);
            if (Arr::get($res, 'Result', false) != User::NAME_ID_NUMBER_MATCH) {
                $this->outPut(ResponseCode::REAL_USER_CHECK_FAIL);
            }
        }
    }

    private function canViewTom($user, $thread, $payType, $paid)
    {
        if ($payType != Thread::PAY_FREE) {//付费贴
            $canFreeViewThreadDetail = $this->canFreeViewTom($user, $thread);
            if ($canFreeViewThreadDetail || $paid) {
                return true;
            } else {
                return false;
            }
        } else {
            $repo = new UserRepository();
            $canViewThreadDetail = $repo->canViewThreadDetail($user, $thread);
            if ($canViewThreadDetail) {
                return true;
            } else {
                return false;
            }
        }
    }

    private function canFreeViewTom($user, $thread)
    {
        $repo = new UserRepository();
        return $repo->canFreeViewPosts($user, $thread);
    }

    private function getFavoriteField($threadId, $loginUser)
    {
        $userId = $loginUser->id;
        return $this->loginDataExists($this->loginUserData, ThreadHelper::EXIST_THREAD_USERS, $threadId, function () use ($userId, $threadId) {
            return ThreadUser::query()->where(['user_id' => $userId, 'thread_id' => $threadId])->exists();
        });
//        return DzqCache::exists(CacheKey::LIST_THREADS_V3_THREAD_USERS . $userId, $threadId, function () use ($userId, $threadId) {
//            return ThreadUser::query()->where(['thread_id' => $threadId, 'user_id' => $userId])->exists();
//        });
    }

    private function getCategoryNameField($categoryId)
    {
        $categories = Category::getCategories();
        $categories = array_column($categories, null, 'id');
        return $categories[$categoryId]['name'] ?? null;
    }

    private function getParentCategory($categoryId)
    {
        $categories = Category::getCategories();
        $categories = array_column($categories, null, 'id');
        $parentCategoryId   = !empty($categories[$categoryId]['parentid']) ? $categories[$categoryId]['parentid'] : 0;
        $parentCategoryName = ! empty($parentCategoryId) ? $categories[$parentCategoryId]['name'] : '';
        return [
            'parentCategoryId'      => $parentCategoryId,
            'parentCategoryName'    => $parentCategoryName
        ];
    }

    /**
     * @desc 获取操作权限
     * @param User $loginUser
     * @param $thread
     * @return bool[]
     */
    private function getAbilityField(User $loginUser, $thread)
    {
        /** @var UserRepository $userRepo */
        $userRepo = app(UserRepository::class);
        /** @var SettingsRepository $settingRepo */
        $settingRepo = app(SettingsRepository::class);

        return [
            'canEdit' => $userRepo->canEditThread($loginUser, $thread),
            'canDelete' => $userRepo->canHideThread($loginUser, $thread),
            'canEssence' => $userRepo->canEssenceThread($loginUser, $thread['category_id']),
            'canStick' => $userRepo->canStickThread($loginUser),
            'canReply' => $userRepo->canReplyThread($loginUser, $thread['category_id']),
            'canViewPost' => $userRepo->canViewThreadDetail($loginUser, $thread),
            'canBeReward' => (bool)$settingRepo->get('site_can_reward'),
            'canFreeViewPost' => $userRepo->canFreeViewPosts($loginUser, $thread)
        ];
    }

    private function threadPayStatus($loginUser, $thread, &$paid)
    {
        $payType = Thread::PAY_FREE;
        $userId = $loginUser->id;
        $threadId = $thread['id'];
        $thread['price'] > 0 && $payType = Thread::PAY_THREAD;
        $thread['attachment_price'] > 0 && $payType = Thread::PAY_ATTACH;
        $canFreeViewTom = $this->canFreeViewTom($loginUser, $thread);
        if ($payType == Thread::PAY_FREE) {
            $paid = null;
        } elseif ($payType != Thread::PAY_FREE && $canFreeViewTom) {
            $paid = true;
        } else {
            $paid = $this->loginDataExists($this->loginUserData, ThreadHelper::EXIST_PAY_ORDERS, $threadId, function () use ($userId, $threadId) {
                return Order::query()
                    ->where([
                        'thread_id' => $threadId,
                        'user_id' => $userId,
                        'status' => Order::ORDER_STATUS_PAID
                    ])->whereIn('type', [Order::ORDER_TYPE_THREAD, Order::ORDER_TYPE_ATTACHMENT])->exists();
            });
//            $paid = DzqCache::exists(CacheKey::LIST_THREADS_V3_USER_PAY_ORDERS . $userId, $threadId, function () use ($userId, $threadId) {
//                return Order::query()
//                    ->where([
//                        'thread_id' => $threadId,
//                        'user_id' => $userId,
//                        'status' => Order::ORDER_STATUS_PAID
//                    ])->whereIn('type', [Order::ORDER_TYPE_THREAD, Order::ORDER_TYPE_ATTACHMENT])->exists();
//            });
        }
        return $payType;
    }

    /**
     * @desc 显示在帖子上的标签，目前支持 付费/精华/红包/悬赏 四种
     * @param $thread
     * @param $tags
     * @return bool[]
     */
    private function getDisplayTagField($thread, $tags)
    {
        $obj = [
            'isPrice' => false,
            'isEssence' => false,
            'isRedPack' => null,
            'isReward' => null
        ];
        if ($thread['price'] > 0 || $thread['attachment_price'] > 0) {
            $obj['isPrice'] = true;
        }
        if ($thread['is_essence']) {
            $obj['isEssence'] = true;
        }
        $tags = array_column($tags, 'tag');
        if (!empty($tags)) {
            if (in_array(TomConfig::TOM_REDPACK, $tags)) {
                $obj['isRedPack'] = true;
            }
            if (in_array(TomConfig::TOM_REWARD, $tags)) {
                $obj['isReward'] = true;
            }
        }
        return $obj;
    }

    private function getContentField($loginUser, $thread, $post, $tomInput, $payType, $paid, $canViewTom, $canFreeViewTom)
    {
        $content = [
            'text' => null,
            'indexes' => null
        ];
        if ($payType == Thread::PAY_FREE || $loginUser->id == $thread['user_id']) {
            $content['text'] = $post['content'];
            $content['indexes'] = $this->tomDispatcher($tomInput, $this->SELECT_FUNC, $thread['id'], null, $canViewTom);
        } else {
            if ($paid || $canFreeViewTom) {
                $content['text'] = $post['content'];
                $content['indexes'] = $this->tomDispatcher($tomInput, $this->SELECT_FUNC, $thread['id'], null, $canViewTom);
            } else {
                $text = '';
                if ($payType == Thread::PAY_ATTACH) {
                    $text = $post['content'];
                } else if ($payType == Thread::PAY_THREAD) {
                    $freeWords = $thread['free_words'];
                    if (floatval($freeWords) > 0) {
                        $text = strip_tags($post['content']);
                        $freeLength = mb_strlen($text) * $freeWords;
                        $text = mb_substr($text, 0, $freeLength) . Post::SUMMARY_END_WITH;
                    }
                }
                $content['text'] = $text;
                //如果有红包和图片，则只显示红包和图片
                $tomConfig = [];
                isset($tomInput[TomConfig::TOM_REDPACK]) && $tomConfig += [TomConfig::TOM_REDPACK => $tomInput[TomConfig::TOM_REDPACK]];
                isset($tomInput[TomConfig::TOM_IMAGE]) && $tomConfig += [TomConfig::TOM_IMAGE => $tomInput[TomConfig::TOM_IMAGE]];
                if (!empty($tomConfig)) {
                    $content['indexes'] = $this->tomDispatcher(
                        $tomConfig,
                        $this->SELECT_FUNC,
                        $thread['id'],
                        null,
                        $canViewTom
                    );
                }
            }
        }
        $content['text'] = str_replace(['<r>', '</r>', '<t>', '</t>'], ['', '', '', ''], $content['text']);
        //考虑到升级V3，帖子的type 都要转为 99，所以针对 type 为 99 的也需要处理图文混排
        if (!empty($content['text'])) {
            $xml = $content['text'];
            $tom_image_key = $body = '';
            if (!empty($content['indexes'])) {
                foreach ($content['indexes'] as $key => $val) {
                    if ($val['tomId'] == TomConfig::TOM_IMAGE) {
                        $body = $val['body'];
                        $tom_image_key = $key;
                    }
                }
            }
            if (!empty($body)) {
                $attachments_body = $body;
                $attachments = array_combine(array_column($attachments_body, 'id'), array_column($attachments_body, 'url'));
                $isset_attachment_ids = [];
                $xml = preg_replace_callback(
                    '<img src="(.*?)" alt="(.*?)" title="(\d+)">',
                    function ($m) use ($attachments, &$isset_attachment_ids) {
                        if (!empty($m)) {
                            $id = trim($m[3], '"');
                            $isset_attachment_ids[] = $id;
                            return 'img src="' . $attachments[$id] . '" alt="' . $m[2] . '" title="' . $id . '"';
                        }
                    },
                    $xml
                );
                //针对图文混排的情况，这里要去掉外部图片展示
//                if (!empty($tom_image_key)) unset($content['indexes'][$tom_image_key]);
                $content['text'] = $xml;
                if(!empty($isset_attachment_ids) && isset($content['indexes'][TomConfig::TOM_IMAGE]['body'])){
                    foreach ($content['indexes'][TomConfig::TOM_IMAGE]['body'] as $k => $v){
                        if(in_array($v['id'], $isset_attachment_ids))       unset($content['indexes'][TomConfig::TOM_IMAGE]['body'][$k]);
                    }
                }
            }
        }


        return $content;
    }

    private function getGroupInfoField($loginUser, $group, $thread)
    {
        $groupResult = null;
        if (!empty($group) && $group['groups']['is_display']) {
            if ( $thread['is_anonymous'] == Thread::IS_ANONYMOUS && $loginUser['id'] != $thread['user_id'] ){
                return $groupResult;
            }
            $groupResult = [
                'groupId' => $group['group_id'],
                'groupName' => $group['groups']['name'],
                'groupIcon' => $group['groups']['icon'],
                'isDisplay' => $group['groups']['is_display']
            ];
        }
        return $groupResult;
    }

    private function getUserInfoField($loginUser, $user, $thread)
    {
        $userResult = [
            'nickname' => '匿名用户'
        ];
        //非匿名用户
        if ((!$thread['is_anonymous'] && !empty($user)) || $loginUser->id == $thread['user_id']) {
            $userResult = [
                'userId' => $user['id'],
                'nickname' => !empty($user['nickname']) ? $user['nickname'] : $user['username'],
                'avatar' => $user['avatar'],
                'threadCount' => $user['thread_count'],
                'followCount' => $user['follow_count'],
                'fansCount' => $user['fans_count'],
                'likedCount' => $user['liked_count'],
                'questionCount' => $user['question_count'],
                'isRealName' => !empty($user['realname']),
                'joinedAt' => date('Y-m-d H:i:s', strtotime($user['joined_at']))
            ];
        }
        return $userResult;
    }

    private function getLikeRewardField($thread, $post)
    {
        $ret = [
            'users' => [],
            'likePayCount' => $post['like_count'] + $thread['rewarded_count'] + $thread['paid_count'],
            'shareCount' => $thread['share_count'],
            'postCount' => $thread['post_count'] - 1
        ];
        $threadId = $thread['id'];
        $postId = $post['id'];
        $postUsers = DzqCache::hGet(CacheKey::LIST_THREADS_V3_POST_USERS, $threadId, function ($threadId) use ($postId, $post) {
            $ret = ThreadHelper::getThreadLikedDetail($threadId, $postId, $post, false);
            return $ret[$threadId] ?? [];
        });
        $ret['users'] = $postUsers;
        return $ret;
    }

    /**
     * @desc 查询是否需要审核
     * @param $title
     * @param $text
     * @param null $isApproved 是否进审核
     * @return array
     */
    private function boolApproved($title, $text, &$isApproved = null)
    {
        /** @var Censor $censor */
        $censor = app(Censor::class);
        $sep = '__' . mt_rand(111111, 999999) . '__';
        $contentForCheck = $title . $sep . $text;
        [$newTitle, $newContent] = explode($sep, $censor->checkText($contentForCheck));
        $isApproved = $censor->isMod;
        return [$newTitle, $newContent];
    }

    private function isReward($loginUser, $thread)
    {
        if (empty($loginUser) || empty($thread)) {
            return false;
        }
        $userId = $loginUser->id;
        $threadId = $thread['id'];
        return $this->loginDataExists($this->loginUserData, ThreadHelper::EXIST_REWARD_ORDERS, $threadId, function () use ($userId, $threadId) {
            return Order::query()->where(['user_id' => $userId, 'type' => Order::ORDER_TYPE_REWARD, 'thread_id' => $threadId, 'status' => Order::ORDER_STATUS_PAID])->exists();
        });
        /*    return DzqCache::exists(CacheKey::LIST_THREADS_V3_USER_REWARD_ORDERS . $userId, $threadId, function () use ($userId, $threadId) {
                return Order::query()->where(['user_id' => $userId, 'type' => Order::ORDER_TYPE_REWARD, 'thread_id' => $threadId, 'status' => Order::ORDER_STATUS_PAID])->exists();
            });*/
    }

    private function loginDataExists($loginUserData, $type, $key, callable $callBack)
    {
        if (array_key_exists($type, $loginUserData)) {
            return isset($loginUserData[$type][$key]);
        } else {
            return $callBack();
        }
    }

    private function isLike($loginUser, $post)
    {
        if (empty($loginUser) || empty($post)) {
            return false;
        }
        $userId = $loginUser->id;
        $postId = $post['id'];
        return $this->loginDataExists($this->loginUserData, ThreadHelper::EXIST_POST_USERS, $postId, function () use ($userId, $postId) {
            return PostUser::query()->where('post_id', $postId)->where('user_id', $userId)->exists();
        });
        /*        return DzqCache::exists(CacheKey::LIST_THREADS_V3_POST_LIKED . $userId, $postId, function () use ($userId, $postId) {
                    return PostUser::query()->where('post_id', $postId)->where('user_id', $userId)->exists();
                });*/
    }

    private function saveTopic($thread, $content)
    {
        $threadId = $thread['id'];
        $topics = $this->optimizeTopics($content['text']);
        $userId = $this->user->id;
        $topicIds = [];
        foreach ($topics as $topicItem) {
            $topicName = str_replace('#', '', $topicItem);

            $topic = Topic::query()->where('content', $topicName)->first();
            if (empty($topic)) {
                //话题名称长度超过20就不创建了
                if (mb_strlen($topicName) > 18) {
                    \Discuz\Common\Utils::outPut(ResponseCode::INVALID_PARAMETER, '创建话题长度不能超过18个字符');
                }
                $topic = new Topic();
                $topic->user_id = $userId;
                $topic->content = $topicName;
                $topic->thread_count = 1;
                $topic->save();
            } else {
                $topic->increment('thread_count');
            }
            $topicId = $topic->id;
            $topicIds[] = $topicId;
            $attr = ['thread_id' => $threadId, 'topic_id' => $topicId];
            ThreadTopic::query()->where($attr)->firstOrCreate($attr);

            $html = sprintf('<span id="topic" value="%s">#%s#</span>', $topic->id, $topic->content);
            if (!strpos($content['text'],$html)){
                $content['text'] = str_replace($topicItem, $html,$content['text']);
            }
        }

        if (empty($topicIds)) {
            ThreadTopic::query()->where('thread_id', $threadId)->delete();
        } else {
            ThreadTopic::query()->where('thread_id', $threadId)->whereNotIn('topic_id', $topicIds)->delete();
        }

        return $content;
    }

    //发帖@用户发送通知消息
    private function sendNews($thread, $post)
    {
        //如果是草稿或需要审核 不发送消息
        if ($thread->is_draft == Thread::IS_DRAFT || $thread->is_approved == Thread::UNAPPROVED || empty($post->parsedContent)) {
            return;
        }
        $this->sendRelated($post, $this->user);
    }

    /*
     * @desc 前端新编辑器只能上传完整url的emoji
     * 后端需要将其解析出代号进行存储
     * @param $text
     */
    private function optimizeEmoji($text)
    {
//        $text = '<r>' . $text . '</r>';
        preg_match_all('/<img((?![<|>]).)*?emoji\/qq((?![<|>]).)*?>/i', $text, $m1);
        $searches = $m1[0];
        $replaces = [];
        foreach ($searches as $search) {
            preg_match('/:[a-z]+?:/i', $search, $m2);
            $emoji = $m2[0];
            $replaces[] = $emoji;
        }
        $text = str_replace($searches, $replaces, $text);
        return $text;
    }

    private function optimizeTopics($text)
    {
        preg_match_all('/#.+?#/', $text, $m1);
        $topics = $m1[0];
        $topics = array_values($topics);
        return $topics;
    }

    private function getPendingOrderInfo($thread)
    {
        return Order::query()
            ->where('thread_id', $thread['id'])
            ->where('status', Order::ORDER_STATUS_PENDING)
            ->whereIn('type', [Order::ORDER_TYPE_REDPACKET, Order::ORDER_TYPE_QUESTION_REWARD, Order::ORDER_TYPE_MERGE])
            ->select(['payment_sn', 'order_sn', 'amount', 'type', 'id', 'status'])
            ->first();
    }

    /**
     * 获取红包/悬赏/混合支付对应的订单，一对一
     * @param $thread
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|object|null
     */
    private function getOrderInfo($thread)
    {
        return Order::query()
            ->where('thread_id', $thread['id'])
            ->whereIn('type', [Order::ORDER_TYPE_REDPACKET, Order::ORDER_TYPE_TEXT, Order::ORDER_TYPE_LONG, Order::ORDER_TYPE_QUESTION_REWARD, Order::ORDER_TYPE_MERGE])
            ->select(['payment_sn', 'order_sn', 'amount', 'type', 'id', 'status'])
            ->first();
    }

    private function renderTopic($text)
    {
        preg_match_all('/#.+?#/', $text, $topic);
        if (empty($topic)) {
            return $text;
        }
        $topic = $topic[0];
        $topic = str_replace('#', '', $topic);
        $topics = Topic::query()->select('id', 'content')->whereIn('content', $topic)->get()->map(function ($item) {
            $item['content'] = '#' . $item['content'] . '#';
            $item['html'] = sprintf('<span id="topic" value="%s">%s</span>', $item['id'], $item['content']);
            return $item;
        })->toArray();
        foreach ($topics as $val) {
            $text = preg_replace("/{$val['content']}/", $val['html'], $text, 1);
        }
        return $text;
    }

    private function renderCall($text)
    {
        preg_match_all('/@.+? /', $text, $call);
        if (empty($call)) {
            return $text;
        }
        $call = $call[0];
        $call = str_replace(['@', ' '], '', $call);
        $ats = User::query()->select('id', 'username')->whereIn('username', $call)->get()->map(function ($item) {
            $item['username'] = '@' . $item['username'];
            $item['html'] = sprintf('<span id="member" value="%s">%s</span>', $item['id'], $item['username']);
            return $item;
        })->toArray();
        foreach ($ats as $val) {
            $text = str_replace($val['username'], $val['html'], $text);
        }
        return $text;
    }
}

