<?php
namespace App\Api\Controller\TopicV3;

use App\Common\ResponseCode;
use App\Models\AdminActionLog;
use App\Models\Topic;
use App\Repositories\UserRepository;
use Discuz\Auth\Exception\PermissionDeniedException;
use Discuz\Base\DzqController;

class BatchDeleteTopicController extends DzqController
{
    protected function checkRequestPermissions(UserRepository $userRepo)
    {
        if (!$this->user->isAdmin()) {
            throw new PermissionDeniedException('您没有批量删除话题的权限');
        }
        return true;
    }

    public function main()
    {
        $ids = $this->inPut("ids");
        if(empty($ids)){
            return $this->outPut(ResponseCode::INVALID_PARAMETER);
        }
        $idsArr = explode(",", $ids);

        foreach ($idsArr as $key=>$value) {
            $action_desc = "";
            $topic = Topic::query()->where("id",$value)->first();
            if(!$topic){
                continue;
            }
            $topicContent = $topic->content;
            $topic->delete();
            AdminActionLog::createAdminActionLog(
                $this->user->id,
                '删除话题【'. $topicContent .'】'
            );
        }

        return $this->outPut(ResponseCode::SUCCESS);
    }

}
