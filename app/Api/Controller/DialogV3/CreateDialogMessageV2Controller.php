<?php

namespace App\Api\Controller\DialogV3;

use App\Commands\Dialog\CreateDialogMessage;
use App\Common\ResponseCode;
use App\Common\Utils;
use App\Models\DialogMessage;
use App\Providers\DialogMessageServiceProvider;
use App\Repositories\UserRepository;
use Discuz\Base\DzqController;
use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Contracts\Validation\Factory;
use Illuminate\Validation\ValidationException;

class CreateDialogMessageV2Controller extends DzqController
{
    /**
     * @var Dispatcher
     */
    protected $bus;

    /**
     * @var Factory
     */
    protected $validation;

    public $providers = [
        DialogMessageServiceProvider::class,
    ];

    public function __construct(Dispatcher $bus, Factory $validation)
    {
        $this->bus = $bus;
        $this->validation = $validation;
    }

    protected function checkRequestPermissions(UserRepository $userRepo)
    {
        if ($this->user->isGuest()) {
            $this->outPut(ResponseCode::JUMP_TO_LOGIN);
        }
        return $userRepo->canCreateDialog($this->user);
    }

    public function main()
    {
        $user = $this->user;
        $data = $this->request->getParsedBody()->toArray();

        try {
            $this->validation->make($data, [
                'dialogId' => 'required|int',
                'messageText'  => 'sometimes|max:450',
                'imageUrl'     => 'required_with:attachmentId|string',
                'attachmentId' => 'required_with:imageUrl|int|min:1',
                'isImage' => 'required|bool'
            ])->validate();
        } catch (ValidationException $e) {
            $this->outPut(ResponseCode::INVALID_PARAMETER, $e->validator->getMessageBag()->first());
        }

        if (!$data['isImage'] && empty($data['messageText']) && empty($data['attachmentId'])) {
            $this->outPut(ResponseCode::INVALID_PARAMETER, '发送内容不能为空！');
        }

        if (!empty($data['messageText']) || 
           (!empty($data['attachmentId']) && !empty($data['imageUrl']))) {
            $data['status'] = DialogMessage::NORMAL_MESSAGE;
        } else {
            $data['status'] = DialogMessage::EMPTY_MESSAGE;
        }

        try {
            $data = Utils::arrayKeysToSnake($data);
            $dialogMessage = $this->bus->dispatch(
                new CreateDialogMessage($user, $data)
            );
        } catch (\Exception $e) {
            $this->outPut(ResponseCode::INVALID_PARAMETER, $e->getMessage());
        }

        $this->outPut(ResponseCode::SUCCESS, '已发送', ['dialogMessageId' => $dialogMessage->id]);
    }
}
