<?php
namespace App\Api\Controller\TopicV3;

use App\Common\ResponseCode;
use App\Models\AdminActionLog;
use App\Models\Topic;
use App\Repositories\UserRepository;
use Discuz\Auth\Exception\PermissionDeniedException;
use Discuz\Base\DzqController;

class BatchUpdateTopicController extends DzqController
{
    protected function checkRequestPermissions(UserRepository $userRepo)
    {
        if (!$this->user->isAdmin()) {
            throw new PermissionDeniedException('您没有批量修改话题的权限');
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

        $isRecommended = $this->inPut("isRecommended");

        foreach ($idsArr as $key=>$value) {
            $action_desc = "";
            $topic = Topic::query()->where("id",$value)->first();

            if($topic->recommended == $isRecommended){
                continue;
            }

            if ($isRecommended || $isRecommended == 0) {
                $topic->recommended = (bool)$isRecommended ? 1 : 0;
                $topic->recommended_at = date('Y-m-d H:i:s', time());
            }
            if($topic->recommended == 1){
                $action_desc = '推荐话题【'. $topic->content .'】';
            }else{
                $action_desc = '取消推荐话题【'. $topic->content .'】';
            }
            $topic->save();

            if($action_desc !== '' && !empty($action_desc)) {
                AdminActionLog::createAdminActionLog(
                    $this->user->id,
                    $action_desc
                );
            }
        }

        return $this->outPut(ResponseCode::SUCCESS);
    }

}
