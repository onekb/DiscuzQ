<?php


namespace App\Api\Controller\UsersV3;

use App\Common\ResponseCode;
use App\Events\Users\UserFollowCreated;
use App\Models\DenyUser;
use App\Models\User;
use App\Models\UserFollow;
use App\Repositories\UserRepository;
use Discuz\Base\DzqController;
use Discuz\Foundation\EventsDispatchTrait;
use Illuminate\Contracts\Events\Dispatcher;

class CreateUserFollowController extends DzqController
{
    use EventsDispatchTrait;

    /**
     * @var Dispatcher
     */
    protected $bus;

    /**
     * @var UserFollow 
     */
    protected $userFollow;

    protected $denyUser;

    public function __construct(Dispatcher $bus,UserFollow $userFollow,DenyUser $denyUser)
    {
        $this->bus = $bus;
        $this->userFollow = $userFollow;
        $this->denyUser = $denyUser;
    }

    protected function checkRequestPermissions(UserRepository $userRepo)
    {
        if ($this->user->isGuest()){
            $this->outPut(ResponseCode::JUMP_TO_LOGIN);
        }
        return $userRepo->canFollowUser($this->user);
    }

    public function main(){

        $actor = $this->user;

        $to_user_id = $this->inPut('toUserId');

        if ($actor->id == $to_user_id) {
            $this->outPut(ResponseCode::NOT_FOLLOW_YOURSELE);
        }

        /** @var User $toUser */
        $toUser = User::query()->where('id', $to_user_id)->first();

        if (!$toUser) {
            $this->outPut(ResponseCode::NOT_FOLLOW_USER);
        }

        //你的屏蔽
        $Mdeny = $this->denyUser->where(['user_id' => $actor->id, 'deny_user_id' => $to_user_id])->first();
        if($Mdeny){
            $this->outPut(ResponseCode::YOU_BLOCKED_HIM);
        }

         //对方屏蔽
        $Udeny = $this->denyUser->where(['user_id' => $to_user_id, 'deny_user_id' => $actor->id])->first();
        if($Udeny){
            $this->outPut(ResponseCode::HAS_BEEN_BLOCKED_BY_THE_OPPOSITION);
        }

        //判断是否已经关注
        $toFromUserFollow = $this->userFollow->where(['to_user_id'=>$to_user_id,'from_user_id'=>$actor->id])->first();
        if($toFromUserFollow){
            $this->outPut(ResponseCode::RESOURCE_EXIST);
        }

        //判断是否需要设置互相关注
        $toUserFollow = $this->userFollow->where(['from_user_id'=>$to_user_id,'to_user_id'=>$actor->id])->first();
        $is_mutual = UserFollow::NOT_MUTUAL;
        if ($toUserFollow) {
            $is_mutual = UserFollow::MUTUAL;
            $toUserFollow->is_mutual = $is_mutual;
            $toUserFollow->save();
        }

        $userFollow = $this->userFollow->firstOrCreate(
            ['from_user_id'=>$actor->id,'to_user_id'=>$to_user_id],
            ['is_mutual'=>$is_mutual]
        );
        $userFollows['id'] = $userFollow['id'];
        $userFollows['fromUserId'] = $userFollow['from_user_id'];
        $userFollows['toUserId'] = $userFollow['to_user_id'];
        $userFollows['isMutual'] = $userFollow['is_mutual'];
        $userFollows['updatedAt'] = $userFollow['updated_at'];
        $userFollows['createdAt'] = $userFollow['created_at'];

        $this->bus->dispatch(
            new UserFollowCreated($actor, $toUser)
        );

        return $this->outPut(ResponseCode::SUCCESS,'', $userFollows);
    }
}
