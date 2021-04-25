<?php
/**
 * Copyright (C) 2020 Tencent Cloud.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace App\Notifications\Messages\MiniProgram;

use App\Models\Order;
use Discuz\Notifications\Messages\SimpleMessage;
use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Support\Arr;

class ReceiveRedPacketMiniProgramMessage extends SimpleMessage
{
    protected $model;

    protected $actor;

    protected $data;

    /**
     * @var UrlGenerator
     */
    protected $url;

    public function __construct(UrlGenerator $url)
    {
        $this->url = $url;
    }

    public function setData(...$parameters)
    {
        [$firstData, $actor, $model, $data] = $parameters;
        // set parent tpl data
        $this->firstData = $firstData;
        $this->actor = $actor;
        $this->model = $model;
        $this->data = $data;

        $this->template();
    }

    public function template()
    {

        return ['content' => $this->getMiniProgramContent()];
    }

    protected function titleReplaceVars()
    {
        return [];
    }

    public function contentReplaceVars($data)
    {

        $message = Arr::get($this->data, 'message', '');
        $threadId = Arr::get($this->data, 'raw.thread_id', 0);
        $actualAmount = Arr::get($this->data, 'raw.actual_amount', 0); // 实际金额

        // 获取支付类型
        $orderName = Order::enumType(Arr::get($this->data, 'raw.type', 0), function ($args) {
            return $args['value'];
        });

        $actorName = Arr::get($this->data, 'raw.actor_username', '');  // 发送人姓名

        // 主题ID为空时跳转到首页
        if (empty($threadId)) {
            $threadUrl = $this->url->to('');
        } else {
            $threadUrl = $this->url->to('/topic/index?id=' . $threadId);
        }

        /**
         * 设置父类 模板数据
         * @parem $user_name
         * @parem $order_type_name
         * @parem $actual_amount
         * @parem $content
         */
        $this->setTemplateData([
            '{$username}'           => $actorName,
            '{$order_type_name}'     => $orderName,
            '{$actual_amount}'       => $actualAmount,
            '{$content}'             => $this->strWords($message),
        ]);
        // build data
        $expand = [
            'redirect_url' => $threadUrl,
        ];

        return $this->compiledArray($expand);
    }

}
