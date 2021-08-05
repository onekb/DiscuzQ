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

use App\Api\Controller as ApiController;

// 判断是否已配置腾讯云  CheckQcloudController
//$route->get('/checkQcloud', 'checkQcloud',  ApiController\CheckQcloudController::class);

//视频转码回调
$route->post('/threads/notify/video', 'threads.notify.video', ApiController\Threads\Notify\ThreadVideoNotifyController::class);

//微信支付回调
$route->post('/trade/notify/wechat', 'trade.notify.wechat', ApiController\Trade\Notify\WechatNotifyController::class);

$route->get('/oauth/wechat/web/user/event', 'wechat.web.user.event', ApiController\Users\WechatWebUserLoginEventController::class);

$route->post('/oauth/wechat/web/user/event', 'wechat.web.user.postevent', ApiController\Users\WechatWebUserLoginPostEventController::class);
