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


use App\Common\ResponseCode;
use App\Models\Category;
use App\Models\Permission;
use App\Models\Post;
use App\Models\Setting;
use App\Models\Thread;
use App\Repositories\UserRepository;
use Discuz\Base\DzqController;

class ThreadStickController extends DzqController
{
    use ThreadTrait;

    protected function checkRequestPermissions(UserRepository $userRepo)
    {
        return true;
    }

    public function main()
    {
        $categoryIds = $this->inPut('categoryIds');
        $threads = Thread::query()->select(['id', 'category_id', 'title', 'updated_at','price','attachment_price','free_words'])->orderByDesc('updated_at');
        if (!empty($categoryIds)) {
            if (!is_array($categoryIds)) {
                $categoryIds = [$categoryIds];
            }
        }

        $isMiniProgramVideoOn = Setting::isMiniProgramVideoOn();
        if (!$isMiniProgramVideoOn) {
            $threads = $threads->where('type', '<>', Thread::TYPE_OF_VIDEO);
        }
        $permissions = Permission::getUserPermissions($this->user);
        $categoryIds = Category::instance()->getValidCategoryIds($this->user, $categoryIds);
        if (!$categoryIds) {
            $this->outPut(ResponseCode::SUCCESS, '', []);
        } else {
            $threads = $threads->whereIn('category_id', $categoryIds);
        }

        $threads = $threads
            ->where('is_sticky', Thread::BOOL_YES)
            ->whereNull('deleted_at')
            ->whereNotNull("user_id")
            ->where('is_draft', Thread::BOOL_NO)
            ->where('is_display', Thread::BOOL_YES)
            ->where('is_approved', Thread::BOOL_YES)
            ->get();
        $threadIds = $threads->pluck('id')->toArray();
        $posts = Post::query()
            ->whereIn('thread_id', $threadIds)
            ->whereNull('deleted_at')
            ->where('is_first', Post::FIRST_YES)
            ->get()->pluck(null, 'thread_id');
        $data = [];
        $linkString = '';
        foreach ($threads as $thread) {
            $title = $thread['title'];
            $id = $thread['id'];
            if (empty($title)) {
                if (isset($posts[$id])) {
                    $title = Post::instance()->getContentSummary($posts[$id]);
                }
            }
            $payType = $this->threadPayStatus($this->user, $thread, $paid);
            if ($payType == Thread::PAY_THREAD) {
                $freeWords = floatval($thread['free_words']);
                if ($freeWords >= 0 && $freeWords < 1) {
                    $title = strip_tags($title);
                    $freeLength = mb_strlen($title) * $freeWords;
                    $title = mb_substr($title, 0, $freeLength) . Post::SUMMARY_END_WITH;
                    //针对最后的表情被截断的情况做截断处理
                    $title = preg_replace('/([^\w])\:\w*\.\.\./s', '$1...', $title);
                    //处理内容开头是表情，表情被截断的情况
                    $title = preg_replace('/^\:\w*\.\.\./s', '...', $title);
                }
            }
            $linkString .= $title;
            $data [] = [
                'threadId' => $thread['id'],
                'categoryId' => $thread['category_id'],
                'title' => $title,
                'updatedAt' => date('Y-m-d H:i:s', strtotime($thread['updated_at'])),
                'canViewPosts' => $this->canViewPosts($thread, $permissions)
            ];
        }
//        [$search, $replace] = Thread::instance()->getReplaceString($linkString);
//        foreach ($data as &$item) {
//            $item['title'] = str_replace($search, $replace, $item['title']);
//        }
        $this->outPut(ResponseCode::SUCCESS, '', $data);
    }


    private function canViewPosts($thread, $permissions)
    {
        if ($this->user->isAdmin() || $this->user->id == $thread['user_id']) {
            return true;
        }
        $viewPostStr = 'category' . $thread['category_id'] . '.thread.viewPosts';
        if (in_array('thread.viewPosts', $permissions) || in_array($viewPostStr, $permissions)) {
            return true;
        }
        return false;
    }
}
