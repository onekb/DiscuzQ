<?php

namespace App\Api\Controller\NotificationV3;

use App\Common\ResponseCode;
use App\Repositories\UserRepository;
use App\Models\NotificationTpl;
use Discuz\Auth\Exception\PermissionDeniedException;
use Discuz\Base\DzqController;
use Discuz\Wechat\EasyWechatTrait;

class ResourceNotificationTplV3Controller extends DzqController
{
    use NotificationTrait;
    use EasyWechatTrait;

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
        $type_name = $this->inPut('typeName');
        $type = $this->inPut('type');

        $tpl = NotificationTpl::query();

        $tpl->when($type != '', function ($query) use ($type) {
            $query->where('type', $type);
        });

        $typeNames = explode(',', $type_name);

        $query = $tpl->whereIn('type_name', $typeNames)->orderBy('type');

        $data = $query->get();

        /**
         * 检测是否存在小程序通知，查询小程序模板变量 key 值
         *
         * @URL 订阅消息参数值内容限制说明: https://developers.weixin.qq.com/miniprogram/dev/api-backend/open-api/subscribe-message/subscribeMessage.send.html
         */
        $miniProgram = $data->where('type', NotificationTpl::MINI_PROGRAM_NOTICE);
        if ($miniProgram->isNotEmpty()) {
            $data->map(function ($item) {
                if ($item->type == NotificationTpl::MINI_PROGRAM_NOTICE) {
                    $keys = $this->getMiniProgramKeys($item);
                    $item->keys = $keys;
                }
            });
        }

        $res = $this->getDefaultAttributes($data);
        $arr = [];
        foreach ($res as $k => $v) {
            $arr[$k] = $v['templateVariables'];
        }
        $res = $this->camelData($this->getDefaultAttributes($data));
        foreach ($res as $k => &$v) {
            $v['templateVariables'] = $arr[$k];
        }

        $this->outPut(ResponseCode::SUCCESS, '', $res);
    }
}
