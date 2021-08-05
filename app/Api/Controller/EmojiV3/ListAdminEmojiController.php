<?php


namespace App\Api\Controller\EmojiV3;

use App\Common\ResponseCode;
use App\Models\Emoji;
use App\Repositories\UserRepository;
use Discuz\Base\DzqController;

class ListAdminEmojiController extends DzqController
{
    protected function checkRequestPermissions(UserRepository $userRepo)
    {
        return $this->user->isAdmin();
    }

    public function main()
    {
        $emojis = Emoji::getEmojiListForController($this->request);
        $result = $this->camelData($emojis);
        $this->outPut(ResponseCode::SUCCESS, '', $result);
    }
}
