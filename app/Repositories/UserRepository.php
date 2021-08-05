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

namespace App\Repositories;

use App\Common\PermissionKey;
use App\Models\Attachment;
use App\Models\Group;
use App\Models\Thread;
use App\Models\User;
use Discuz\Foundation\AbstractRepository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Arr;

class UserRepository extends AbstractRepository
{
    /**
     * Get a new query builder for the users table.
     *
     * @return Builder
     */
    public function query()
    {
        return User::query();
    }

    /**
     * Find a user by ID, optionally making sure it is visible to a certain
     * user, or throw an exception.
     *
     * @param int $id
     * @param User $actor
     * @return Builder|\Illuminate\Database\Eloquent\Model|User
     *
     * @throws ModelNotFoundException
     */
    public function findOrFail($id, User $actor = null)
    {
        $query = User::where('id', $id);

        return $this->scopeVisibleTo($query, $actor)->firstOrFail();
    }

    /**
     * Find a user by an identification (username or phone number).
     *
     * @param array $param
     * @return User|null
     */
    public function findByIdentification($param)
    {
        return User::where($param)->first();
    }

    /**
     * 检查 XXX || categoryX.XXX 的权限
     *
     * @param User $user
     * @param string $ability
     * @param null $categoryId
     * @return bool
     */
    private function checkCategoryPermission(User $user, string $ability, $categoryId = null)
    {
        $abilities = [$ability];

        if ($categoryId) {
            $abilities[] = 'category'.$categoryId.'.'.$ability;
        }

        return $user->hasPermission($abilities, false);
    }

    /**
     * 发帖权限
     *
     * @param User $user
     * @param null $categoryId
     * @return bool
     */
    public function canCreateThread(User $user, $categoryId = null)
    {
        return $this->checkCategoryPermission($user, PermissionKey::CREATE_THREAD, $categoryId);
    }

    /**
     * 发帖插入图片权限
     *
     * @param User $user
     * @return bool
     */
    public function canInsertImageToThread(User $user)
    {
        return $this->checkCategoryPermission($user, PermissionKey::THREAD_INSERT_IMAGE);
    }

    /**
     * 发帖插入视频权限
     *
     * @param User $user
     * @return bool
     */
    public function canInsertVideoToThread(User $user)
    {
        return $this->checkCategoryPermission($user, PermissionKey::THREAD_INSERT_VIDEO);
    }

    /**
     * 发帖插入音频权限
     *
     * @param User $user
     * @param null $categoryId
     * @return bool
     */
    public function canInsertAudioToThread(User $user)
    {
        return $this->checkCategoryPermission($user, PermissionKey::THREAD_INSERT_AUDIO);
    }

    /**
     * 发帖插入附件权限
     *
     * @param User $user
     * @param null $categoryId
     * @return bool
     */
    public function canInsertAttachmentToThread(User $user)
    {
        return $this->checkCategoryPermission($user, PermissionKey::THREAD_INSERT_ATTACHMENT);
    }

    public function canDeleteAttachment(User $user, $attachment)
    {
        if ($attachment->user_id == $user->id || $user->isAdmin()) {
            return true;
        }

        // 有权编辑帖子时，允许删除帖子下的附件
        $postAttachmentTypes = [
            Attachment::TYPE_OF_FILE,
            Attachment::TYPE_OF_IMAGE,
            Attachment::TYPE_OF_AUDIO,
            Attachment::TYPE_OF_VIDEO,
        ];
        return true;

        // if (in_array($attachment->type, $postAttachmentTypes) && $this->canEditPost($user, $attachment->post)) {
        //     return true;
        // }
    }

    /**
     * 发帖插入商品权限
     *
     * @param User $user
     * @param null $categoryId
     * @return bool
     */
    public function canInsertGoodsToThread(User $user)
    {
        return $this->checkCategoryPermission($user, PermissionKey::THREAD_INSERT_GOODS);
    }

    /**
     * 发帖插入付费权限
     *
     * @param User $user
     * @param null $categoryId
     * @return bool
     */
    public function canInsertPayToThread(User $user)
    {
        return $this->checkCategoryPermission($user, PermissionKey::THREAD_INSERT_PAY);
    }

    /**
     * 发帖插入悬赏权限
     *
     * @param User $user
     * @param null $categoryId
     * @return bool
     */
    public function canInsertRewardToThread(User $user)
    {
        return $this->checkCategoryPermission($user, PermissionKey::THREAD_INSERT_REWARD);
    }

    /**
     * 发帖插入红包权限
     *
     * @param User $user
     * @param null $categoryId
     * @return bool
     */
    public function canInsertRedPacketToThread(User $user)
    {
        return $this->checkCategoryPermission($user, PermissionKey::THREAD_INSERT_RED_PACKET);
    }

    /**
     * 发帖插入位置权限
     *
     * @param User $user
     * @param null $categoryId
     * @return bool
     */
    public function canInsertPositionToThread(User $user)
    {
        return $this->checkCategoryPermission($user, PermissionKey::THREAD_INSERT_POSITION);
    }

    /**
     * 匿名发帖权限
     *
     * @param User $user
     * @param null $categoryId
     * @return bool
     */
    public function canCreateThreadAnonymous(User $user, $categoryId = null)
    {
        return $this->checkCategoryPermission($user, PermissionKey::THREAD_ALLOW_ANONYMOUS, $categoryId);
    }

    /**
     * 查看帖子权限
     *
     * @param User $user
     * @param null $categoryId
     * @return bool
     */
    public function canViewThreads(User $user, $categoryId = null)
    {
        return $this->checkCategoryPermission($user, PermissionKey::VIEW_THREADS, $categoryId);
    }

    /**
     * 免费查看付费帖子权限
     *
     * @param User $user
     * @param array|Thread $thread
     * @return bool
     */
    public function canFreeViewPosts(User $user, $thread)
    {
        // 是作者自己，或者有对应权限
        return ($user->id == $thread['user_id'])
            || $this->checkCategoryPermission($user, PermissionKey::THREAD_FREE_VIEW_POSTS, $thread['category_id']);
    }

    /**
     * 收藏帖子权限
     *
     * @param User $user
     * @return bool
     */
    public function canFavoriteThread(User $user)
    {
        return $user->hasPermission(PermissionKey::THREAD_FAVORITE);
    }

    /**
     * 帖子点赞权限
     *
     * @param User $user
     * @return bool
     */
    public function canLikePosts(User $user)
    {
        return $user->hasPermission(PermissionKey::THREAD_LIKE_POSTS);
    }

    /**
     * 帖子加精权限
     *
     * @param User $user
     * @param null $categoryId
     * @return bool
     */
    public function canEssenceThread(User $user, $categoryId = null)
    {
        return $this->checkCategoryPermission($user, PermissionKey::THREAD_ESSENCE, $categoryId);
    }

    /**
     * 帖子置顶权限
     *
     * @param User $user
     * @return bool
     */
    public function canStickThread(User $user)
    {
        return $user->hasPermission(PermissionKey::THREAD_STICKY);
    }

    /**
     * 编辑帖子权限
     *
     * @param User $user
     * @param $thread
     * @return bool
     */
    public function canEditThread(User $user, $thread)
    {
        if (!$thread) {
            return false;
        }

        if ($thread['is_draft'] == Thread::BOOL_YES) {
            if ($thread['user_id'] == $user->id) {
                return true;
            }
            if ($thread['is_approved'] == Thread::BOOL_NO && $user->isAdmin()) {
                return true;
            }
        } else {
            if ($user->isAdmin()) {
                return true;
            }
            if ($thread['user_id'] == $user->id) {
                if ($this->checkCategoryPermission($user, PermissionKey::THREAD_EDIT_OWN, $thread['category_id']) || 
                    $this->checkCategoryPermission($user, PermissionKey::THREAD_EDIT, $thread['category_id'])) {
                    return true;
                }
            } else {
                return $thread['is_approved'] == Thread::BOOL_YES && $this->checkCategoryPermission($user, PermissionKey::THREAD_EDIT, $thread['category_id']);
            }
        }

        return false;
    }

    /**
     * 编辑前台帖子权限(自己)
     *
     * @param User $user
     * @param $categoryId
     * @return bool
     */
    public function canEditMyThread(User $user, $categoryId = null)
    {
        return $this->checkCategoryPermission($user, PermissionKey::THREAD_EDIT_OWN, $categoryId);
    }

     /**
     * 编辑前台帖子权限(自己+他人)
     *
     * @param User $user
     * @param $categoryId
     * @return bool
     */
    public function canEditOthersThread(User $user, $categoryId = null)
    {
        return $this->checkCategoryPermission($user, PermissionKey::THREAD_EDIT, $categoryId);
    }

    /**
     * 前台删除帖子权限
     *
     * @param User $user
     * @param $thread
     * @return bool
     */
    public function canHideThread(User $user, $thread)
    {
        if (!$thread) {
            return false;
        }

        // 删除自己的草稿
        if (Arr::get($thread, 'is_draft') && (Arr::get($thread, 'user_id') == $user->id)) {
            return true;
        }

        return ($user->id === $thread['user_id'] && $this->checkCategoryPermission($user, PermissionKey::THREAD_HIDE_OWN, $thread['category_id']))
            || $this->checkCategoryPermission($user, PermissionKey::THREAD_HIDE, $thread['category_id']);
    }

    /**
     * 回复帖子权限
     *
     * @param User $user
     * @param $categoryId
     * @return bool
     */
    public function canReplyThread(User $user, $categoryId)
    {
        return $this->checkCategoryPermission($user, PermissionKey::THREAD_REPLY, $categoryId);
    }

    public function canViewThreadDetail(User $user, $thread)
    {
        if (!$thread) {
            return false;
        }

        // 审核状态下，作者本人与管理员可见
        if (Arr::get($thread, 'is_approved') == Thread::UNAPPROVED) {
            return $thread['user_id'] == $user->id || $user->isAdmin();
        }

        // 是本人，且（没有删除或者是自己删除的）
        if ($thread['user_id'] == $user->id
            && (!Arr::get($thread, 'deleted_at') || Arr::get($thread, 'deleted_user_id') == $user->id)
        ) {
            return true;
        }

        // 查看自己的草稿
        if (Arr::get($thread, 'is_draft')) {
            return $thread['user_id'] == $user->id;
        }

        return $this->checkCategoryPermission($user, PermissionKey::THREAD_VIEW_POSTS, $thread['category_id']);
    }

    public function canViewListWallet(User $user)
    {
        return $user->hasPermission(PermissionKey::WALLET_VIEW_LIST);
    }

    public function canViewListLogs(User $user)
    {
        return $user->hasPermission(PermissionKey::WALLET_LOGS_VIEW_LIST);
    }

    public function canViewListCash(User $user)
    {
        return $user->hasPermission(PermissionKey::CASH_VIEW_LIST);
    }

    /**
     * 删除回复权限
     *
     * @param User $user
     * @param $post
     * @return bool
     */
    public function canHidePost(User $user, $post)
    {
        if (!$post) {
            return false;
        }
        // 首帖按主题权限走
        if ($post->is_first) {
            return $this->canEditThread($user, $post->thread);
        }

        // 是作者本人且拥有编辑自己主题或回复的权限
        if ($post->user_id == $user->id && $this->checkCategoryPermission($user, PermissionKey::THREAD_HIDE_OWN, $post->thread->category_id)) {
            return true;
        }

        return $this->checkCategoryPermission($user, PermissionKey::THREAD_HIDE_POSTS, $post->thread->category_id);
    }

    /**
     * 删除用户组权限
     *
     * @param User $user
     * @param array $ids
     * @return bool
     */
    public function canDeleteGroup(User $user, $ids)
    {
        $groups = [
            Group::ADMINISTRATOR_ID,
            Group::BAN_ID,
            Group::UNPAID,
            Group::GUEST_ID,
            Group::MEMBER_ID,
        ];

        $disabled = array_intersect($groups, $ids);

        return empty($disabled) && $user->isAdmin();
    }

    public function canCreateGroup(User $user)
    {
        return $user->isAdmin();
    }

    public function canEditGroup(User $user)
    {
        return $user->isAdmin();
    }

    public function canListGroup(User $user)
    {
        return $user->isAdmin();
    }

    public function canCreateInviteUserScale(User $user)
    {
        return $user->hasPermission(PermissionKey::CREATE_INVITE_USER_SCALE);
    }


    public function canCreateInviteAdminUserScale(User $user)
    {
        return $user->isAdmin();
    }

    /**
     * 下单权限
     *
     * @param User $user
     * @return bool
     */
    public function canCreateOrder(User $user)
    {
        return $user->hasPermission(PermissionKey::ORDER_CREATE);
    }

    public function canPayOrder(User $user)
    {
        return $user->hasPermission(PermissionKey::TRADE_PAY_ORDER);
    }

    public function canCreateStopWord(User $user)
    {
        return $user->isAdmin();
    }

    public function canDeleteStopWord(User $user)
    {
        return $user->isAdmin();
    }

    public function canExportStopWord(User $user)
    {
        return $user->isAdmin();
    }

    public function canViewStopWord(User $user)
    {
        return $user->isAdmin();
    }

    public function canEditStopWord(User $user)
    {
        return $user->isAdmin();
    }

    public function canWalletPay(User $user)
    {
        return $user->status == 0 && $user->pay_password;
    }

    public function canCreateDialog(User $user)
    {
        return $user->hasPermission(PermissionKey::DIALOG_CREATE);
    }

    public function canCreateCash(User $user)
    {
        return $user->hasPermission(PermissionKey::CASH_CREATE);
    }

    public function canViewUser(User $user)
    {
        return $user->hasPermission(PermissionKey::USER_VIEW);
    }

    public function canFollowUser(User $user)
    {
        return $user->hasPermission(PermissionKey::USER_FOLLOW_CREATE);
    }

    /**
     * 发布内容是否需要验证码
     */
    public function canCreateThreadWithCaptcha(User $user)
    {
        if ($user->isAdmin()) {
            return false;
        } else {
            return $user->hasPermission(PermissionKey::CREATE_THREAD_WITH_CAPTCHA);
        }
    }

    /**
     * 发布内容是否需要绑定手机
     */
    public function canCreateThreadNeedBindPhone(User $user)
    {
        if ($user->isAdmin() || !empty($user->mobile)) {
            return false;
        } else {
            return $user->hasPermission(PermissionKey::PUBLISH_NEED_BIND_PHONE);
        }
    }


    /**
     * 上传头像与删除权限
     */

    public function canCreateAvatar(User $user)
    {
        return $user->isAdmin();
    }

    public function canDeleteAvatar(User $user)
    {
        return $user->isAdmin();
    }


    public function canExportUser(User $user)
    {
        return $user->isAdmin();
    }


    public function canUserWallet(User $user)
    {
        return $user->isAdmin();
    }


    public function canUpdateUserWallet(User $user)
    {
        return $user->isAdmin();
    }


    public function canListUserScren(User $user)
    {
        return $user->isAdmin();
    }


    public function canUserStatus(User $user)
    {
        return $user->isAdmin();
    }
}
