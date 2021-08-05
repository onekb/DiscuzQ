<?php

namespace App\Api\Controller\DialogV3;

use App\Commands\Dialog\CreateDialog;
use App\Common\ResponseCode;
use App\Providers\DialogMessageServiceProvider;
use App\Repositories\UserRepository;
use Discuz\Base\DzqController;
use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Contracts\Validation\Factory;
use Illuminate\Validation\ValidationException;

class CreateDialogV2Controller extends DzqController
{
    protected $validation;

    /**
     * @var Dispatcher
     */
    protected $bus;

    public $providers = [
        DialogMessageServiceProvider::class,
    ];

    public function __construct(Dispatcher $bus, Factory $validation)
    {
        $this->validation = $validation;
        $this->bus = $bus;
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
        $actor = $this->user;
        $data = [
            'message_text'=>$this->inPut('messageText'),
            'recipient_username'=>$this->inPut('recipientUsername'),
            'isImage'=>$this->inPut('isImage'),
            'image_url' => $this->inPut('imageUrl') ?? '',
            'attachment_id' => $this->inPut('attachmentId') ?? 0
        ];

        if(empty($data['recipient_username'])){
            $this->outPut(ResponseCode::INVALID_PARAMETER);
        }

        try {
            $this->validation->make($data, [
                'message_text'  => 'sometimes:messageText|max:450',
                'image_url'     => 'required_with:attachment_id|string',
                'attachment_id' => 'required_with:image_url|int|min:1',
                'isImage' => 'required|bool'
            ])->validate();
        } catch (ValidationException $e) {
            $this->outPut(ResponseCode::INVALID_PARAMETER, $e->validator->getMessageBag()->first());
        }

        if (!$data['isImage'] && empty($data['message_text']) && empty($data['attachment_id'])) {
            $this->outPut(ResponseCode::INVALID_PARAMETER, '发送内容不能为空！');
        }

        try {
          $res = $this->bus->dispatch(
                new CreateDialog($actor, $data)
            );
        } catch (\Exception $e) {
            $this->outPut(ResponseCode::INVALID_PARAMETER, $e->getMessage());
        }

        $data = [
            'dialogId' => $res['dialogId'],
            'dialogMessageId' => $res['dialogMessageId']
        ];

        $this->outPut(ResponseCode::SUCCESS, '已发送', $data);
    }
}
