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

namespace App\Api\Controller\AttachmentV3;

use App\Common\ResponseCode;
use App\Models\Order;
use App\Models\Thread;
use App\Models\ThreadTom;
use App\Models\AttachmentShare;
use App\Modules\ThreadTom\TomConfig;
use App\Repositories\UserRepository;
use Carbon\Carbon;
use Discuz\Base\DzqController;
use Illuminate\Contracts\Routing\UrlGenerator;

class ShareAttachmentController extends DzqController
{

    protected $url;

    public function __construct(UrlGenerator $url)
    {
        $this->url = $url;
    }

    protected function checkRequestPermissions(UserRepository $userRepo)
    {
        if ($this->user->isGuest()) {
            $this->outPut(ResponseCode::JUMP_TO_LOGIN);
        }

        $threadId =$this->inPut('threadId');
        $user = $this->user;

        $thread = Thread::query()
            ->from('threads as th')
            ->whereNull('th.deleted_at')
            ->where(['th.id' => $threadId, 'th.is_draft' => Thread::BOOL_NO, 'th.is_approved' => Thread::BOOL_YES])
            ->leftJoin('thread_tom as tt','tt.thread_id','=','th.id')
            ->where(['tom_type' => TomConfig::TOM_DOC , 'status' => ThreadTom::STATUS_ACTIVE])
            ->first(['th.user_id', 'th.price', 'th.attachment_price', 'th.category_id', 'tt.value']);

        if (!$thread) {
            $this->outPut(ResponseCode::RESOURCE_NOT_FOUND);
        }

        if (!$user->isAdmin() && $user->id !== $thread->user_id){
            //是否付费
            if ( $thread->price > 0 || $thread->attachment_price > 0 ) {
                $isPay = Order::query()
                    ->whereIn('type',[Order::ORDER_TYPE_THREAD, Order::ORDER_TYPE_ATTACHMENT])
                    ->where([ 'thread_id' => $threadId, 'status' => Order::ORDER_STATUS_PAID])
                    ->exists();
                if (!$isPay) $this->outPut(ResponseCode::UNAUTHORIZED);
            } else if (!$userRepo->canViewThreadDetail($user, $thread)) {
                $this->outPut(ResponseCode::UNAUTHORIZED);
            }
        }

        $this->thread = $thread;
        return true;
    }

    public function main()
    {
        $user = $this->user;
        $data = [
            'threadId' => $this->inPut('threadId'),
            'attachmentsId' => $this->inPut('attachmentsId')
        ];

        $this->dzqValidate($data,[
            'threadId' => 'required|int',
            'attachmentsId' => 'required|int'
        ]);

        $count = AttachmentShare::query()
            ->where(['attachments_id' => $data['attachmentsId'], 'user_id' => $user->id])
            ->where('created_at', '>=', Carbon::now()->modify('-1 minutes'))
            ->count('attachments_id');

        if ($count >= 2) $this->outPut(ResponseCode::NET_ERROR,'操作太快，请稍后再试');
        
        $docValue = json_decode($this->thread->value,true);

        if (!isset($docValue['docIds']) || !is_array($docValue['docIds']) || !in_array($data['attachmentsId'], $docValue['docIds'])) {
            $this->outPut(ResponseCode::RESOURCE_NOT_FOUND);
        }

        $sign = $this->sign($data);

        $AttachmentShare = new AttachmentShare;
        $AttachmentShare->sign = $sign;
        $AttachmentShare->attachments_id = $data['attachmentsId'];
        $AttachmentShare->user_id = $user->id;
        $AttachmentShare->expired_at = Carbon::now()->modify('+10 minutes');
        $AttachmentShare->save();

        $this->outPut(ResponseCode::SUCCESS, '', [
            'url' => $this->url->to('/apiv3/attachment.download') . '?sign=' . $sign . '&attachmentsId=' . $data['attachmentsId']
        ]);


    }
    //生成唯一标识
    public function sign($data)
    {
        $stringArr = openssl_random_pseudo_bytes(16);
        $stringArr[6] = chr(ord($stringArr[6]) & 0x0f | 0x40); // set version to 0100
        $stringArr[8] = chr(ord($stringArr[8]) & 0x3f | 0x80); // set bits 6-7 to 10
        $string =  vsprintf('%s%s%s%s%s%s%s%s',
            str_split(bin2hex($stringArr), 4));
        return md5($string.$data['threadId'].$data['attachmentsId']);
    }
}
