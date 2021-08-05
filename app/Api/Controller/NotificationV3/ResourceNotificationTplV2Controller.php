<?php

namespace App\Api\Controller\NotificationV3;

use App\Common\ResponseCode;
use App\Models\NotificationTpl;
use App\Notifications\Messages\TemplateVariables;
use Discuz\Auth\AssertPermissionTrait;
use Discuz\Auth\Exception\PermissionDeniedException;
use Discuz\Base\DzqController;

class ResourceNotificationTplV2Controller extends DzqController
{
    use AssertPermissionTrait;
    use TemplateVariables;

    /**
     * 禁止修改的系统通知，只能设置开启关闭
     *
     * @var int[]
     */
    protected $disabledId = [25, 26, 27, 28, 33, 34, 37, 39, 41, 43, 45, 47, 49];

    public function main()
    {
        try {
            $this->assertAdmin($this->user);
        } catch (PermissionDeniedException $e) {
            $this->outPut(ResponseCode::JUMP_TO_LOGIN);
        }

        $type_name = $this->inPut('typeName');

        $tpl = NotificationTpl::query()->where('type_name', $type_name)->orderBy('type')->get();
        $tpl = $tpl->map(function (NotificationTpl $i) {
            $trans = 'template_variables.'.($this->templateVariables[$i->id] ?? '');

            return [
                'id' => $i->id,
                'status' => $i->status,
                'type' => $i->type,
                'typeName' => $i->type_name,
                'title' => $i->title,
                'content' => $i->content,
                'templateId' => $i->template_id,
                'templateVariables' => $trans === 'template_variables.' ? [] : trans($trans),
                'firstData' => $i->first_data,
                'keywordsData' => $i->keywords_data ? explode(',', $i->keywords_data) : [],
                'remarkData' => $i->remark_data,
                'color' => $i->color ?: [],
                'redirectType' => (int) $i->redirect_type,
                'redirectUrl' => (string) $i->redirect_url,
                'pagePath' => (string) $i->page_path,
                'disabled' => $i->type === NotificationTpl::SYSTEM_NOTICE && in_array($i->id, $this->disabledId),
            ];
        });
        $this->outPut(ResponseCode::SUCCESS, '', $tpl);
    }
}
