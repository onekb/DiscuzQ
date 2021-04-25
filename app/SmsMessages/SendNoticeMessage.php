<?php

/**
 * Copyright (C) 2020 Tencent Cloud.
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *   http://www.apache.org/licenses/LICENSE-2.0
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace App\SmsMessages;

use Illuminate\Support\Arr;
use Overtrue\EasySms\Contracts\GatewayInterface;
use Overtrue\EasySms\Message;
use Overtrue\EasySms\Strategies\OrderStrategy;

class SendNoticeMessage extends Message
{
    /**
     * 传进来的数据
     * @var array
     *
     * template_id 模板ID
     * variable 模板中的变量
     */
    protected $data;

    protected $strategy = OrderStrategy::class; // 定义本短信的网关使用策略，覆盖全局配置中的 `default.strategy`

    protected $gateways = ['qcloud']; // 定义本短信的适用平台，覆盖全局配置中的 `default.gateways`

    public function __construct($data)
    {
        $this->data = $data;
    }

    // 定义直接使用内容发送平台的内容
    public function getContent(GatewayInterface $gateway = null)
    {
        return '';
    }

    // 定义使用模板发送方式平台所需要的模板 ID
    public function getTemplate(GatewayInterface $gateway = null)
    {
        return Arr::get($this->data, 'template_id');
    }

    // 模板参数
    public function getData(GatewayInterface $gateway = null)
    {
        return collect(Arr::get($this->data, 'variable'))->sortKeys()->values()->toArray();
    }
}
