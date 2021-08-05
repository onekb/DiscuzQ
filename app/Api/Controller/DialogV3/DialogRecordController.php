<?php

namespace App\Api\Controller\DialogV3;

use App\Common\ResponseCode;
use App\Models\Dialog;
use App\Models\DialogMessage;
use App\Models\User;
use App\Repositories\UserRepository;
use Discuz\Auth\Exception\NotAuthenticatedException;
use Discuz\Base\DzqController;

class DialogRecordController extends DzqController
{
    protected function checkRequestPermissions(UserRepository $userRepo)
    {
        if ($this->user->isGuest()) {
            $this->outPut(ResponseCode::JUMP_TO_LOGIN);
        }
        return true;
    }

    public function main()
    {
        $actor = $this->user;

        $username = $this->inPut('username');
        $user = User::query()->where('username', $username)->pluck('id')->toArray();

        if(empty($user)){
            $this->outPut(ResponseCode::INVALID_PARAMETER,'用户不存在');
        }
        $userId = $user[0];
        if ($userId == $actor->id) {
            $this->outPut(ResponseCode::INVALID_PARAMETER,'自己不能给自己发私信');
        }

        $dialogs = Dialog::query()
            ->where(['sender_user_id' => $actor->id, 'recipient_user_id' => $userId])
            ->orWhere(function ($query) use ($actor, $userId) {
                $query->where(['sender_user_id' => $userId, 'recipient_user_id' => $actor->id]);
            })
            ->first();

        $DialogM = DialogMessage::query()->distinct('dialog_id')
            ->where('dialog_id', $dialogs['id'])->pluck('dialog_id')->toArray();


        $data = [];
        if (empty($DialogM)){
            $data['dialogId'] = "";
        }else{
            $data['dialogId'] = $DialogM[0];
        }

        $this->outPut(ResponseCode::SUCCESS,'', $data);
    }

}
