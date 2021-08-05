<?php

namespace App\Api\Controller\DialogV3;

use App\Common\ResponseCode;
use App\Models\Dialog;
use App\Models\DialogMessage;
use App\Models\User;
use App\Providers\DialogMessageServiceProvider;
use App\Repositories\DialogMessageRepository;
use App\Repositories\DialogRepository;
use App\Repositories\UserRepository;
use Discuz\Auth\Exception\NotAuthenticatedException;
use Discuz\Base\DzqController;

class ListDialogMessageV2Controller extends DzqController
{
    /**
     * @var DialogRepository
     */
    protected $dialogs;

    /**
     * @var DialogMessageRepository
     */
    protected $dialogMessage;

    public $providers = [
        DialogMessageServiceProvider::class,
    ];

    public function __construct(DialogRepository $dialogs, DialogMessageRepository $dialogMessage)
    {
        $this->dialogs = $dialogs;
        $this->dialogMessage = $dialogMessage;
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

        $filters = $this->inPut('filter') ?: [];
        $page = $this->inPut('page') ?: 1;
        $perPage = $this->inPut('perPage') ?: 10;

        if(empty($filters) || empty($filters['dialogId'])){
            $this->outPut(ResponseCode::INVALID_PARAMETER);
        }
        $dialogData = Dialog::query()->where("id",$filters['dialogId'])->first();
        if(empty($dialogData)){
            $this->outPut(ResponseCode::INVALID_PARAMETER, '对话ID'.$filters['dialogId'].'记录不存在');
        }

        //设置登录用户已读
        $dialog = $this->dialogs->findOrFail($filters['dialogId'], $user);
        if ($dialog->sender_user_id == $user->id) {
            $type = 'sender';
        } else {
            $type = 'recipient';
        }
        $dialog->setRead($type);

        $pageData = $this->search($user, $filters, $dialog, $perPage, $page);
        $this->outPut(ResponseCode::SUCCESS, '', $pageData);
    }

    public function search(User $user, $filters, $dialog, $perPage, $page)
    {
        $query = $this->dialogMessage->query()
            ->with([
                'user:id,username,avatar,avatar_at',
            ]);

        $query->select('dialog_message.*');
        $query->where('dialog_id', $filters['dialogId']);

        $query->join(
            'dialog',
            'dialog.id',
            '=',
            'dialog_message.dialog_id'
        )->where(function ($query) use ($user) {
            $query->where('dialog.sender_user_id', $user->id);
            $query->orWhere('dialog.recipient_user_id', $user->id);
        });

        // 按照登陆用户的删除情况过滤数据
        if ($dialog->sender_user_id == $user->id && $dialog->sender_deleted_at) {
            $query->whereColumn(
                'dialog_message.created_at',
                '>',
                'dialog.sender_deleted_at'
            );
        }
        if ($dialog->recipient_user_id == $user->id && $dialog->recipient_deleted_at) {
            $query->whereColumn(
                'dialog_message.created_at',
                '>',
                'dialog.recipient_deleted_at'
            );
        }

        $query->orderBy('dialog_message.id', 'desc');

        $pageData = $this->messagePagination($page, $perPage, $query, false);
        $pageData['pageData'] = $pageData['pageData']->map(function (DialogMessage $i) {

            $user = [
                'id'=>$i->user->id,
                'avatar'=>$i->user->avatar,
                'username'=>$i->user->username,
            ];

            $imageUrl = $i->getImageUrlMessageText($i->attachment_id);
            $messageText = $i->getMessageText();

            return [
                'id' => $i->id,
                'userId' => $i->user_id,
                'dialogId' => $i->dialog_id,
                'attachmentId' => $i->attachment_id,
                'summary' => $i->summary,
                'messageText' => $messageText,
                'messageTextHtml' => $i->formatMessageText(),
                'isImageLoading' => empty($messageText) && empty($imageUrl) ? true : false,
                'imageUrl' => $imageUrl,
                'updatedAt' => optional($i->updated_at)->format('Y-m-d H:i:s'),
                'createdAt' => optional($i->created_at)->format('Y-m-d H:i:s'),
                'user' => $user,
            ];
        });

        return $pageData;
    }

    /*
     * 私信分页数据支持200条
     */
    private function messagePagination($page, $perPage, \Illuminate\Database\Eloquent\Builder $builder, $toArray = true)
    {
        $page = $page >= 1 ? intval($page) : 1;
        $perPageMax = 200;
        $perPage = $perPage >= 1 ? intval($perPage) : 20;
        $perPage > $perPageMax && $perPage = $perPageMax;
        $count = $builder->count();
        $builder = $builder->offset(($page - 1) * $perPage)->limit($perPage)->get();
        $builder = $toArray ? $builder->toArray() : $builder;
        $url = $this->request->getUri();
        $port = $url->getPort();
        $port = $port == null ? '' : ':' . $port;
        parse_str($url->getQuery(), $query);
        $queryFirst = $queryNext = $queryPre = $query;
        $queryFirst['page'] = 1;
        $queryNext['page'] = $page + 1;
        $queryPre['page'] = $page <= 1 ? 1 : $page - 1;

        $path = $url->getScheme() . '://' . $url->getHost() . $port . $url->getPath() . '?';
        return [
            'pageData' => $builder,
            'currentPage' => $page,
            'perPage' => $perPage,
            'firstPageUrl' => $this->buildUrl($path, $queryFirst),
            'nextPageUrl' => $this->buildUrl($path, $queryNext),
            'prePageUrl' => $this->buildUrl($path, $queryPre),
            'pageLength' => count($builder),
            'totalCount' => $count,
            'totalPage' => $count % $perPage == 0 ? $count / $perPage : intval($count / $perPage) + 1
        ];
    }

    private function buildUrl($path, $query)
    {
        return urldecode($path . http_build_query($query));
    }
}
