<?php


namespace App\Api\Controller\ThreadsV3;

use App\Common\ResponseCode;
use App\Models\Thread;
use App\Models\ThreadRedPacket;
use App\Models\ThreadUserViewRecord;
use App\Models\User;
use App\Models\UserWalletLog;
use App\Repositories\UserRepository;
use Carbon\Carbon;
use Discuz\Base\DzqController;

class CheckUserGetRedpacketController extends DzqController
{
    protected $threadId;

    protected $thread;

    protected function checkRequestPermissions(UserRepository $userRepo)
    {
        if ($this->user->isGuest()) {
            $this->outPut(ResponseCode::JUMP_TO_LOGIN);
        }
        if ($this->user->status == User::STATUS_NEED_FIELDS) {
            $this->outPut(ResponseCode::JUMP_TO_SIGIN_FIELDS);
        }
        if ($this->user->status == User::STATUS_MOD) {
            $this->outPut(ResponseCode::JUMP_TO_AUDIT);
        }
        $this->threadId = $this->inPut('threadId');
        $this->thread = Thread::query()
            ->where(['id' => $this->threadId,
                     'is_draft' => Thread::IS_NOT_DRAFT,
                     'is_approved' => Thread::APPROVED])
            ->whereNull('deleted_at')
            ->first();
        if (empty($this->thread)) {
            $this->outPut(ResponseCode::RESOURCE_NOT_FOUND);
        }

        return $userRepo->canViewThreadDetail($this->user, $this->thread);
    }

    public function main()
    {
        $redpacketData = ThreadRedPacket::query()->where(['thread_id' => $this->threadId, 'condition' => 1])->first();
        if (empty($redpacketData)) {
            $this->outPut(ResponseCode::INVALID_PARAMETER, '该帖不是集赞红包帖！');
        }

        $walletLogForRedpacket = UserWalletLog::query()
                ->where(['user_id' => $this->user->id,
                         'change_type' => UserWalletLog::TYPE_REDPACKET_INCOME,
                         'thread_id' => $this->threadId])->first();

        $status = false;
        $afterGetRedPacketFirstEnter = false;
        $threadUserViewRecord = $this->getThreadUserViewRecord();
        if (!empty($walletLogForRedpacket)) {
            $status = true;
            if (empty($threadUserViewRecord) ||
                (isset($threadUserViewRecord['view_at']) && $threadUserViewRecord['view_at'] < $walletLogForRedpacket['created_at'])) {
                $afterGetRedPacketFirstEnter = true;
            }
        }

        $result = [
            'status' => $status,
            'amount' => floatval((string)$walletLogForRedpacket['change_available_amount']) ?? 0,
            'getRedPacketTime' => $walletLogForRedpacket['created_at'] ?? '',
            'afterGetRedPacketFirstEnter' => $afterGetRedPacketFirstEnter
        ];

        $this->updateThreadUserViewRecord();

        $this->outPut(ResponseCode::SUCCESS, '', $result);
    }

    private function getThreadUserViewRecord()
    {
        return ThreadUserViewRecord::query()->where(['thread_id' => $this->threadId, 'user_id' => $this->user->id])->first();
    }

    private function updateThreadUserViewRecord()
    {
        $build = [
            'thread_id' => $this->threadId,
            'user_id' => $this->user->id,
            'view_at' => Carbon::now()
        ];
        ThreadUserViewRecord::query()->updateOrInsert(['thread_id' => $this->threadId, 'user_id' => $this->user->id], $build);
        return true;
    }
}