<?php

namespace App\Api\Controller\NotificationV3;

use App\Common\ResponseCode;
use App\Models\NotificationTpl;
use App\Repositories\UserRepository;
use Discuz\Auth\Exception\PermissionDeniedException;
use Discuz\Base\DzqController;
use Illuminate\Support\Collection;

class ListNotificationTplV3Controller extends DzqController
{
    protected function checkRequestPermissions(UserRepository $userRepo)
    {
        $actor = $this->user;
        if (!$actor->isAdmin()) {
            throw new PermissionDeniedException('没有权限');
        }
        return true;
    }

    public function main()
    {
        $page = $this->inPut('page');
        $perPage = $this->inPut('perPage');

        $tpl = NotificationTpl::all(['id', 'status', 'type', 'type_name', 'is_error', 'error_msg'])
            ->groupBy('type_name');

        $pageData = $this->specialPagination($page, $perPage, $tpl,false);

        $pageData['pageData'] = $this->build($pageData['pageData']);

        $this->outPut(ResponseCode::SUCCESS, '', $this->camelData($pageData));
    }

    private function build(Collection $data)
    {
        return $data->map(function (Collection $item, $index) {
            // Splicing typeName
            $typeStatus = [];
            $errorArr = [];
            $item->each(function ($value) use (&$typeStatus, &$errorArr) {
                /** @var NotificationTpl $value */
                if ($value->status) {
                    $build = [
                        'id' => $value->id,
                        'status' => $value->status,
                        'type' => NotificationTpl::enumTypeName($value->type),
                        'is_error' => $value->is_error,
                        'error_msg' => $value->error_msg,
                    ];
                    array_push($typeStatus, $build);
                }
            });

            return [
                'name' => $index,
                'type_status' => $typeStatus,
            ];
        })->values();
    }

}
