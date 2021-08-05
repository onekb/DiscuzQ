<?php

namespace App\Api\Controller\NotificationV3;

use App\Common\ResponseCode;
use App\Models\NotificationTpl;
use Discuz\Auth\AssertPermissionTrait;
use Discuz\Auth\Exception\PermissionDeniedException;
use Discuz\Base\DzqController;
use Illuminate\Support\Collection;

class ListNotificationTplV2Controller extends DzqController
{
    use AssertPermissionTrait;

    public function main()
    {
        try {
            $this->assertAdmin($this->user);
        } catch (PermissionDeniedException $e) {
            $this->outPut(ResponseCode::JUMP_TO_LOGIN);
        }

        $page = $this->inPut('page') ?: 1;
        $perPage = $this->inPut('perPage') ?: 20;
        $tpl = NotificationTpl::all(['id', 'status', 'type', 'type_name'])->groupBy('type_name');
        $total = $tpl->count();
        $data = $tpl->skip(($page - 1) * $perPage)->take($perPage);
        $data = $this->build($data);

        $this->outPut(ResponseCode::SUCCESS, '', [
            'pageData' => $data,
            'currentPage' => $page,
            'perPage' => $perPage,
            'totalPage' => $total % $perPage == 0 ? $total / $perPage : intval($total / $perPage) + 1,
        ]);
    }

    /**
     * @param Collection $data
     *
     * @return Collection
     */
    private function build(Collection $data)
    {
        return $data->map(function (Collection $item, $index) {
            $typeName = '';
            $item->each(function ($value) use (&$typeName) {
                if ($value->status) {
                    $typeName = $typeName.(string) NotificationTpl::enumTypeName($value->type, '、');
                }
            });

            return [
                'name' => $index,
                'typeStatus' => trim($typeName, '、'),
            ];
        })->values();
    }
}
