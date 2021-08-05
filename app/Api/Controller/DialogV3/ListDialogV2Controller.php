<?php

namespace App\Api\Controller\DialogV3;

use App\Common\ResponseCode;
use App\Models\Dialog;
use App\Models\DialogMessage;
use App\Models\User;
use App\Providers\DialogMessageServiceProvider;
use App\Repositories\UserRepository;
use Discuz\Auth\AssertPermissionTrait;
use Discuz\Auth\Exception\NotAuthenticatedException;
use Discuz\Base\DzqController;

class ListDialogV2Controller extends DzqController
{
    use AssertPermissionTrait;

    public $providers = [
        DialogMessageServiceProvider::class,
    ];

    protected function checkRequestPermissions(UserRepository $userRepo)
    {
        if ($this->user->isGuest()) {
            $this->outPut(ResponseCode::JUMP_TO_LOGIN);
        }
        return true;
    }

    public function main()
    {
        $user = $this->user;

        $page = $this->inPut('page') ?: 1;
        $perPage = $this->inPut('perPage') ?: 10;

        $pageData = $this->search($user, $perPage, $page);

        $this->outPut(ResponseCode::SUCCESS, '', $pageData);
    }

    public function search(User $user, $perPage, $page)
    {
        $query = Dialog::query()
            ->with([
                'sender:id,username,avatar,avatar_at,nickname',
                'recipient:id,username,avatar,avatar_at,nickname',
                'dialogMessage',
            ]);
        $tablePrefix = config('database.connections.mysql.prefix');

        $query->distinct('dialog.id')
            ->select('dialog.*')
            ->join(
                'dialog_message',
                'dialog.id',
                '=',
                'dialog_message.dialog_id'
            )
            ->where('dialog_message_id', '>', 0)
            ->where(function ($query) use ($tablePrefix, $user) {
                $query->where('dialog.sender_user_id', $user->id)
                    ->whereRaw("{$tablePrefix}dialog_message.`created_at` > IFNULL({$tablePrefix}dialog.`sender_deleted_at`, 0 )");
            })
            ->orWhere(function ($query) use ($tablePrefix, $user) {
                $query->where('dialog.recipient_user_id', $user->id)
                    ->whereRaw("{$tablePrefix}dialog_message.`created_at` > IFNULL({$tablePrefix}dialog.`recipient_deleted_at`, 0 )");
            })
            ->orderBy('dialog_message_id', 'desc');

        $pageData = $this->pagination($page, $perPage, $query, false);

        $pageData['pageData'] = $pageData['pageData']->map(function (Dialog $i) {

            $actor = $this->user;

            $dQuery = Dialog::query()
                ->where('sender_user_id',$actor->id)
                ->orWhere('recipient_user_id',$actor->id)
                ->get('id')->toArray();

            $dialogIds = array_column($dQuery, 'id');

            $dMQuery = DialogMessage::query()
                ->whereIn('dialog_id', $dialogIds)
                ->where('user_id','!=',$actor->id)
                ->where('status', DialogMessage::NORMAL_MESSAGE)
                ->get()->toArray();

            $newList = [];
            foreach ($dMQuery as $key => $value) {
                if (isset($newList[$value['user_id']])) {
                    if ($value['read_status'] == 0) {
                        $newList[$value['user_id']]['unreadCount'] = $newList[$value['user_id']]['unreadCount'] + 1;
                    }
                    $newList[$value['user_id']]['message'][] = $value;
                } else {
                    $newList[$value['user_id']]['unreadCount'] = 0;
                    if ($value['read_status'] == 0) {
                        $newList[$value['user_id']]['unreadCount'] = 1;
                    }
                    $newList[$value['user_id']]['message'][] = $value;
                }

            }
            $sendUser = null;
            $recipientUser = null;
            if (!empty($i->sender)) {
                $sendUser = [
                    'id' => $i->sender->id,
                    'avatar' => !empty($i->sender->avatar) ? $i->sender->avatar : "",
                    'username' => $i->sender->username,
                    'nickname' => $i->sender->nickname
                ];
            }
            if (!empty($i->recipient)) {
                $recipientUser = [
                    'id' => $i->recipient->id,
                    'avatar' => !empty($i->recipient->avatar) ? $i->recipient->avatar : "",
                    'username' => $i->recipient->username,
                    'nickname' => $i->recipient->nickname
                ];
            }

            $msg = $i->dialogMessage;
            $msg = $msg
                ? [
                    'id' => $msg->id,
                    'userId' => $msg->user_id,
                    'unreadCount' => $newList[$msg->user_id]['unreadCount'] ?? 0,
                    'dialogId' => $msg->dialog_id,
                    'attachmentId' => $msg->attachment_id,
                    'summary' => $msg->summary,
                    'messageText' => $msg->getMessageText(),
                    'messageTextHtml' => $msg->formatMessageText(),
                    'imageUrl' => $msg->getImageUrlMessageText(),
                    'updatedAt' => optional($msg->updated_at)->format('Y-m-d H:i:s'),
                    'createdAt' => optional($msg->created_at)->format('Y-m-d H:i:s'),
                ]
                : null;

            return [
                'id' => $i->id,
                'dialogMessageId' => $i->dialog_message_id ?: 0,
                'senderUserId' => $i->sender_user_id,
                'recipientUserId' => $i->recipient_user_id,
                'senderReadAt' => optional($i->sender_read_at)->format('Y-m-d H:i:s'),
                'recipientReadAt' => optional($i->recipient_read_at)->format('Y-m-d H:i:s'),
                'updatedAt' => optional($i->updated_at)->format('Y-m-d H:i:s'),
                'createdAt' => optional($i->created_at)->format('Y-m-d H:i:s'),
                'sender' => $sendUser,
                'recipient' => $recipientUser,
                'dialogMessage' => $msg
            ];
        });

        return $pageData;
    }
}
