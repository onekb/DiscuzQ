<?php

namespace App\Api\Controller\DialogV3;

use App\Commands\Dialog\DeleteDialog;
use App\Common\ResponseCode;
use App\Models\Dialog;
use App\Repositories\UserRepository;
use Discuz\Auth\Exception\NotAuthenticatedException;
use Discuz\Base\DzqController;
use Illuminate\Contracts\Bus\Dispatcher;

class DeleteDialogV2Controller extends DzqController
{
    /**
     * @var Dispatcher
     */
    protected $bus;

    public function __construct(Dispatcher $bus)
    {
        $this->bus = $bus;
    }

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
        $id = $this->inPut('id');
        $dialogData = Dialog::query()->where("id",$id)->first();
        if(empty($dialogData)){
            $this->outPut(ResponseCode::INVALID_PARAMETER);
        }

        try {
            $this->bus->dispatch(
                new DeleteDialog($user, $id)
            );
        } catch (\Exception $e) {
            $this->outPut(ResponseCode::SUCCESS, $e->getMessage());
        }

        $this->outPut(ResponseCode::SUCCESS, '已删除');
    }
}
