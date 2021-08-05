<?php
namespace App\Api\Controller\ReportV3;

use App\Common\ResponseCode;
use App\Models\Post;
use App\Models\Report;
use App\Models\Thread;
use App\Repositories\UserRepository;
use Discuz\Base\DzqController;

class CreateReportsController extends DzqController
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
        $userId   = (int)$this->inPut('userId');
        $threadId = (int)$this->inPut('threadId');
        $postId   = $this->inPut('postId') ? (int)$this->inPut('postId') : 0;
        $reason   = $this->inPut('reason');
        $type     = (int)$this->inPut('type');
        if($userId !== $this->user->id){
            $this->outPut(ResponseCode::INTERNAL_ERROR, '用户错误', '');
        }

        $validateData = [
            'reason' => $reason,
            'type' => $type
        ];
        $this->dzqValidate($validateData, [
            'reason'=> 'required_without:message_text|max:450',
            'type'=> 'integer|in:0,1,2'
        ]);

        if ($type == 2 && !$postId) {
            return $this->outPut(ResponseCode::INTERNAL_ERROR, '缺少必要参数', '');
        }

        // 判断
        $threadData = Thread::query()->where('id', $threadId)->first();
        if (!$threadData) {
            $this->outPut(ResponseCode::INVALID_PARAMETER, '');
        }
        if ($type == 2 && $postId) {
            $postData = Post::query()->where('id', $postId)->first();
            if (!$postData) {
                $this->outPut(ResponseCode::INVALID_PARAMETER, '');
            }
        }

        // 判断是否存在,合并理由
        $query = Report::query();

        $exists = $query->where([
            'user_id' => $userId,
            'thread_id' => $threadId,
            'post_id' => $postId,
            'status' => 0
        ])->exists();

        if ($exists) {
            // 若存在，合并理由
            $report = $query->first();
            $report->reason = $reason . '、' . $report->reason;
        } else {
            $report            = new Report();
            $report->user_id   = $userId;
            $report->thread_id = $threadId;
            $report->post_id   = $postId;
            $report->type      = $type;
            $report->reason    = $reason;
        }

        $result = $report->save();
        if (!$result) {
            app('log')->info('requestId：' . $this->requestId . '-' . '创建举报记录出错： ' . $report);
            return $this->outPut(ResponseCode::DB_ERROR, '', '');
        }
        $data['id'] = $report->id;
        return $this->outPut(ResponseCode::SUCCESS, '', $data);
    }
}
