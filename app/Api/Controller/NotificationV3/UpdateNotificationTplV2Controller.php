<?php

namespace App\Api\Controller\NotificationV3;

use App\Common\ResponseCode;
use App\Models\NotificationTpl;
use Discuz\Auth\AssertPermissionTrait;
use Discuz\Auth\Exception\PermissionDeniedException;
use Discuz\Base\DzqController;
use Illuminate\Contracts\Validation\Factory;
use Illuminate\Support\Arr;
use Illuminate\Validation\ValidationException;
use RuntimeException;

class UpdateNotificationTplV2Controller extends DzqController
{
    use AssertPermissionTrait;

    /**
     * @var Factory
     */
    protected $validation;

    public function __construct(Factory $validation)
    {
        $this->validation = $validation;
    }

    public function main()
    {
        try {
            $this->assertAdmin($this->user);
        } catch (PermissionDeniedException $e) {
            $this->outPut(ResponseCode::JUMP_TO_LOGIN);
        }

        $data = $this->inPut('data') ?: [];
        $tpl = NotificationTpl::query()->whereIn('id', Arr::pluck($data, 'id'))->get()->keyBy('id');

        try {
            collect($data)->map(function ($attributes) use ($tpl) {
                if ($notificationTpl = $tpl->get(Arr::get($attributes, 'id'))) {
                    $this->updateTpl($notificationTpl, $attributes);
                }
            });
        } catch (\Exception $e) {
            $this->outPut(ResponseCode::INVALID_PARAMETER, $e->getMessage());
        }

        $this->outPut(ResponseCode::SUCCESS);
    }

    /**
     * @param NotificationTpl $notificationTpl
     * @param array $attributes
     *
     * @return NotificationTpl
     * @throws ValidationException
     */
    protected function updateTpl(NotificationTpl $notificationTpl, array $attributes)
    {
        switch ($notificationTpl->type) {
            case 0:
                $this->validation->make($attributes, [
                    'title' => 'filled',
                ])->validate();

                if (Arr::has($attributes, 'title')) {
                    $notificationTpl->title = Arr::get($attributes, 'title');
                }
                if (Arr::has($attributes, 'content')) {
                    $notificationTpl->content = Arr::get($attributes, 'content');
                }
                break;
            case 1:
                if ($notificationTpl->status == 1) {
                    $this->validation->make($attributes, [
                        'template_id' => 'filled',
                    ])->validate();
                }

                if (Arr::has($attributes, 'template_id')) {
                    $notificationTpl->template_id = Arr::get($attributes, 'template_id');
                }
                break;
        }

        if (isset($attributes['status'])) {
            $status = Arr::get($attributes, 'status');
            if ($status == 1 && $notificationTpl->type == 1 && empty($notificationTpl->template_id)) {
                // 验证是否设置模板ID
                throw new RuntimeException('notification_is_missing_template_config');
            }

            $notificationTpl->status = $status;
        }

        if (isset($attributes['first_data'])) {
            $notificationTpl->first_data = Arr::get($attributes, 'first_data');
        }

        if (isset($attributes['keywords_data'])) {
            $keywords = array_map(function ($keyword) {
                return str_replace(',', '，', $keyword);
            }, (array) Arr::get($attributes, 'keywords_data', []));

            $notificationTpl->keywords_data = implode(',', $keywords);
        }

        if (isset($attributes['remark_data'])) {
            $notificationTpl->remark_data = Arr::get($attributes, 'remark_data');
        }

        if (isset($attributes['color'])) {
            $notificationTpl->color = Arr::get($attributes, 'color');
        }

        if (isset($attributes['redirect_type'])) {
            $notificationTpl->redirect_type = (int) Arr::get($attributes, 'redirect_type');
        }

        if (isset($attributes['redirect_url'])) {
            $notificationTpl->redirect_url = Arr::get($attributes, 'redirect_url');
        }

        if (isset($attributes['page_path'])) {
            $notificationTpl->page_path = Arr::get($attributes, 'page_path');
        }

        $notificationTpl->save();

        return $notificationTpl;
    }
}
