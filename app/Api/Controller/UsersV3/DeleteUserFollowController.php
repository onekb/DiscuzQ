<?php


namespace App\Api\Controller\UsersV3;

use App\Commands\Users\DeleteUserFollow;
use App\Common\ResponseCode;
use App\Models\UserFollow;
use App\Repositories\UserRepository;
use Discuz\Base\DzqController;
use Illuminate\Contracts\Bus\Dispatcher;

class DeleteUserFollowController extends DzqController
{
    protected $bus;

    public function __construct(Dispatcher $bus)
    {
        $this->bus = $bus;
    }

    protected function checkRequestPermissions(UserRepository $userRepo)
    {
        if ($this->user->isGuest()){
            $this->outPut(ResponseCode::JUMP_TO_LOGIN);
        }
        return true;
    }

    public function main(){
        $actor = $this->user;
        $to_user_id = 0;
        $from_user_id = 0;

        $type = (int) $this->inPut("type");
        $typeArr = [1,2];
        if(empty($type) || !in_array($type,$typeArr)){
            $this->outPut(ResponseCode::INVALID_PARAMETER, '');
        }

        if ($type == 1) {
            //删除我的关注
            $to_user_id = (int) $this->inPut("id");
            $toUserData = $this->user->query()
                ->where('id', $to_user_id)->first();
            if (!$toUserData) {
                $this->outPut(ResponseCode::INVALID_PARAMETER,'');
            }
            $toUserDa = UserFollow::query()->where('from_user_id',$actor->id)
                ->where('to_user_id',$to_user_id)
                ->first();
            if(!$toUserDa){
                $this->outPut(ResponseCode::INVALID_PARAMETER,'');
            }
        } elseif ($type == 2) {
            //删除我的粉丝
            $from_user_id = (int) $this->inPut("id");
            $fromUserData = $this->user->query()
                ->where('id', $from_user_id)->first();
            if (!$fromUserData) {
                $this->outPut(ResponseCode::INVALID_PARAMETER,'');
            }
            $fromUserDa = UserFollow::query()->where('to_user_id',$actor->id)
                ->where('from_user_id',$from_user_id)
                ->first();
            if(!$fromUserDa){
                $this->outPut(ResponseCode::INVALID_PARAMETER,'');
            }
        }

        $data = collect();
        $data->push($this->bus->dispatch(
            new DeleteUserFollow($actor, $to_user_id, $from_user_id)
        ));
        return $this->outPut(ResponseCode::SUCCESS,'',$data);
    }
}
