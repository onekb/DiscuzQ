<?php

namespace App\Api\Controller\ThreadsV3;

use App\Common\ResponseCode;
use App\Models\Thread;
use App\Models\User;
use App\Repositories\UserRepository;
use Discuz\Base\DzqController;

class ListFavoritesController extends DzqController
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
        /** @var User $user */
        $user = $this->user;

        $page = $this->inPut('page') ?: 1;
        $perPage = $this->inPut('perPage') ?: 10;

        $query = $user->favoriteThreads()
            ->with(['user'])
            ->orderBy('thread_user.created_at', 'desc');
        $data = $this->pagination($page, $perPage, $query->getQuery(), false);
        /** @var Thread[] $threads */
        $threads = $data['pageData'];
        $results = [];
        foreach ($threads as $thread) {
            $user = $thread->user ? [
                'pid' => $thread->user['id'],
                'userName' => $thread->user['username'],
                'avatar' => $thread->user['avatar'],
                'threadCount' => $thread->user['thread_count'],
                'followCount' => $thread->user['follow_count'],
                'fansCount' => $thread->user['fans_count'],
                'likedCount' => $thread->user['liked_count'],
                'questionCount' => $thread->user['question_count'],
                'joinedAt' => date('Y-m-d H:i:s', strtotime($thread->user['joined_at']))
            ] : null;

            $results[] = [
                'thread' => Thread::instance()->formatThread($thread),
                'user' => $user,
            ];
        }
        $data['pageData'] = $results;

        $this->outPut(ResponseCode::SUCCESS, '', $data);
    }
}
