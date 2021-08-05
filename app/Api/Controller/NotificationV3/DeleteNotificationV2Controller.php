<?php

namespace App\Api\Controller\NotificationV3;

use App\Common\ResponseCode;
use App\Repositories\UserRepository;
use Discuz\Auth\Exception\PermissionDeniedException;
use Discuz\Base\DzqController;
use Illuminate\Notifications\DatabaseNotification;

class DeleteNotificationV2Controller extends DzqController
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
        $ids = explode(',', $this->inPut('id'));

        $user = $this->user;

        $deleted = DatabaseNotification::query()
            ->whereIn('id', $ids)
            ->where('notifiable_id', $user->id)
            ->forceDelete();

        $this->outPut($deleted > 0 ? ResponseCode::SUCCESS : ResponseCode::INVALID_PARAMETER);
    }
}
