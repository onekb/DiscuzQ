<?php

namespace App\Api\Controller\NotificationV3;

use App\Common\ResponseCode;
use App\Common\Utils;
use App\Models\Thread;
use App\Models\User;
use App\Repositories\UserRepository;
use Discuz\Auth\Exception\PermissionDeniedException;
use Discuz\Base\DzqController;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class ListNotificationV2Controller extends DzqController
{

    protected function checkRequestPermissions(UserRepository $userRepo)
    {
        $actor = $this->user;
        if ($actor->isGuest()) {
            $this->outPut(ResponseCode::JUMP_TO_LOGIN);
        }
        return true;
    }

    public function main()
    {
        $user = $this->user;
        $filters = $this->inPut('filter') ?: [];
        $page = $this->inPut('page') ?: 1;
        $perPage = $this->inPut('perPage') ?: 10;

        $pageData = $this->search($user, $filters, $perPage, $page);
        $pageData['pageData'] = $pageData['pageData']->map(function (DatabaseNotification $i) {
            return $this->formatData($i);
        });

        $this->outPut(ResponseCode::SUCCESS, '', $pageData);
    }

    public function search(User $user, $filters, $perPage, $page)
    {
        $type = Arr::get($filters, 'type');

        $query = $user->notifications()
            ->when($type, function ($query, $type) {
                return $query->whereIn('type', explode(',', $type));
            });

        $query->orderBy('created_at', 'desc');

        $pageData = $this->pagination($page, $perPage, $query->getQuery(), false);
        $data = $pageData['pageData'];

        // type markAsRead
        $user->unreadNotifications()->whereIn('type', explode(',', $type))->get()->markAsRead();

        /**
         * 解决 N+1 问题
         * 获取主题&用户
         */
        [$users, $threads] = $this->getUsersAndThreads($data, $type);

        /**
         * 系统通知结构不一
         */
        if ($type != 'system') {
            // 获取通知里当前的用户名称和头像
            $data->map(function ($item) use ($users, $threads, $user, $type) {
                $user = $users->get(Arr::get($item->data, 'user_id'));
                if (!empty($user)) {
                    $item->user_name = $user->username;
                    $item->user_avatar = $user->avatar;
                    $item->realname = $user->realname;
                    $item->nickname = $user->nickname;
                }
                // 判断是否是楼中楼，查询用户名
                if (Arr::has($item->data, 'reply_post_user_id') && Arr::get($item->data, 'reply_post_user_id') != 0) {
                    $replyPostUser = $users->get(Arr::get($item->data, 'reply_post_user_id'));
                    if (!empty($replyPostUser)) {
                        $item->reply_post_user_name = $replyPostUser->username;
                    }
                }

                // 查询主题相关内容
                if (!empty($threadID = Arr::get($item->data, 'thread_id', 0))) {
                    // 获取主题作者用户组
                    if (!empty($threads->get($threadID))) {
                        $thread = $threads->get($threadID);
                        $item->thread_user_nickname = $thread->user->nickname;
                        $item->thread_user_avatar = $thread->user->avatar;
                        $item->thread_type = $thread->type;
                        $item->thread_is_approved = $thread->is_approved;
                        $item->thread_created_at = $thread->created_at;
                        $threadUser = $thread->user;
                        if (!empty($threadUser)) {
                            $item->thread_username = $threadUser->username;
                            $item->thread_user_groups = $threadUser->groups->pluck('name')->join(',');
                            /**
                             * 判断是否是问答、匿名提问
                             * @var Thread $thread
                             */
                            if ($thread->type == Thread::TYPE_OF_QUESTION && !empty($thread->question)) {
                                // 判断如果当前触发通知人又是匿名问答人，就准备匿名用户
                                if ($user->id == $thread->user_id && $thread->is_anonymous) {
                                    // 判断如果是匿名人，但是不是推送的 问答提问通知、也不是财务通知，其余通知都不匿名
                                    if (Str::contains($type, ['questioned', 'rewarded'])) {
                                        $item->user_name = $thread->isAnonymousName();
                                        $item->realname = $thread->isAnonymousName();
                                        $item->user_avatar = '';
                                        $item->isAnonymous = true;
                                    } elseif (Str::contains($type, ['related'])) {
                                        /**
                                         * 判断如果是 @通知 ，当匿名贴@指定人时，指定人看到的通知应该是匿名人@他
                                         * (只用是否是首帖区分@的来自类型)
                                         */
                                        $postId = Arr::get($item->data, 'post_id');
                                        if ($postId == $thread->firstPost->id) {
                                            $item->user_name = $thread->isAnonymousName();
                                            $item->realname = $thread->isAnonymousName();
                                            $item->user_avatar = '';
                                            $item->isAnonymous = true;
                                        }
                                    }
                                }
                                // 匿名主题信息全都匿名
                                $item->thread_username = $thread->isAnonymousName();
                                $item->thread_user_groups = '';
                            }
                        }
                    }
                }
            });
        } else {
            // 获取通知里当前的用户名称和头像
            $data->map(function ($item) use ($users, $threads, $user) {
                if (!empty($threadID = Arr::get($item, 'data.raw.thread_id', 0))) {
                    // 获取主题作者用户组
                    if (!empty($threads->get($threadID))) {
                        $thread = $threads->get($threadID);
                        $item->thread_is_approved = $thread->is_approved;
                        $item->thread_created_at = $thread->created_at;
                    }
                }
            });
        }

        $pageData['pageData'] = $data;

        return $pageData;
    }

    protected function getUsersAndThreads($data, $type)
    {
        if ($type == 'system') {
            $data->where('type', '=', $type);
            $pluck = 'raw.thread_id';
        } else {
            $data->where('type', '<>', $type);
            $pluck = 'thread_id';
        }

        // 非系统通知
        $list = $data->pluck('data');

        // 用户 IDs
        $collectList = collect($list);
        $userIds = $collectList->pluck('user_id');
        $replyUserId = $collectList->pluck('reply_post_user_id');
        $userIds = $userIds->merge($replyUserId)->filter()->unique()->values();
        $users = User::query()->whereIn('id', $userIds)->get()->keyBy('id');

        // 主题 ID
        $threadIds = collect($list)->pluck($pluck)->filter()->unique()->values();
        // 主题及其作者与作者用户组
        $with = ['user', 'user.groups', 'firstPost'];
        // 如果是 question 添加关联查询
        if ($type == 'questioned') {
            array_push($with, 'question');
        }
        $threads = Thread::with($with)->whereIn('id', $threadIds)->get()->keyBy('id');

        return [$users, $threads];
    }

    protected function formatData(DatabaseNotification $data)
    {
        $result = array_merge([
            'id' => $data->id,
            'type' => $data->type,
            'userId' => $data->notifiable_id,
            'readAt' => optional($data->read_at)->format('Y-m-d H:i:s'),
            'createdAt' => optional($data->created_at)->format('Y-m-d H:i:s'),
        ], Utils::arrayKeysToCamel($data->data));

        if(!empty($result['postContent']) && mb_strlen($result['postContent']) > Thread::NOTICE_CONTENT_LENGTH){
            $result['postContent'] = $this->getThreadTitleOrContent($result['postContent'], Thread::NOTICE_CONTENT_LENGTH);
        }

        if(!empty($result['threadTitle']) && mb_strlen($result['threadTitle']) > Thread::TITLE_LENGTH) {
            $result['threadTitle'] = $this->getThreadTitleOrContent($result['threadTitle'], Thread::TITLE_LENGTH);
        }

        if(!empty($result['replyPostContent']) && mb_strlen($result['replyPostContent']) > Thread::NOTICE_CONTENT_LENGTH) {
            $result['replyPostContent'] = $this->getThreadTitleOrContent($result['replyPostContent'], Thread::NOTICE_CONTENT_LENGTH);
        }
        if(!empty($result['content']) && mb_strlen($result['content']) > Thread::NOTICE_CONTENT_LENGTH) {
            $result['content'] = $this->getThreadTitleOrContent($result['content'], Thread::NOTICE_CONTENT_LENGTH);
        }

        // 默认必须要有的字段
        if (!array_key_exists('reply_post_id', $result)) {
            $result = array_merge($result, [
                'replyPostId' => 0,
            ]);
        } else {
            // 返回楼中楼数据
            $result = array_merge($result, [
                'replyPostUserName' => $data->reply_post_user_name,
            ]);
        }

        // 新增单独赋值的字段值
        $result = array_merge($result, [
            'username' => $data->user_name ?: '',
            'userAvatar' => $data->user_avatar ?: '',
            'nickname' => $data->nickname ?: '',
            'isReal' => $this->getIsReal($data->realname),
            'threadUsername' => $data->thread_username ?: '',
            'threadUserGroups' => $data->thread_user_groups ?: '',
            'threadCreatedAt' => optional($data->thread_created_at)->format('Y-m-d H:i:s'),
            'threadIsApproved' => $data->thread_is_approved ?: 0,
            'threadUserNickname' => $data->thread_user_nickname ?: '',
            'threadUserAvatar' => $data->thread_user_avatar ?: '',
        ]);

        // 判断是否要匿名
        if (isset($data->isAnonymous) && $data->isAnonymous) {
            $result['userId'] = -1;
            $result['isReal'] = false; // 全部默认未认证
            $result['isAnonymous'] = $data->isAnonymous;
        }

        return $result;
    }

    /**
     * 是否实名认证
     *
     * @param $realname
     *
     * @return string
     */
    protected function getIsReal($realname)
    {
        if (isset($realname) && $realname != null) {
            return true;
        } else {
            return false;
        }
    }

    private function getThreadTitleOrContent($titleOrContent, $length)
    {
        $titleOrContent = Str::substr(strip_tags($titleOrContent), 0, $length);
        return $titleOrContent;
    }
}
