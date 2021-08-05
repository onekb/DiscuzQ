<?php
/**
 * Copyright (C) 2021 Tencent Cloud.
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

namespace App\Models;


use Discuz\Base\DzqModel;

class ThreadTag extends DzqModel
{
    protected $table = 'thread_tag';

    const TEXT = 100;
    const IMAGE = 101;          // 图片
    const VOICE = 102;          // 语音
    const VIDEO = 103;          // 视频
    const GOODS = 104;          // 商品
    const QA = 105;             // 问答
    const RED_PACKET = 106;     // 红包
    const REWARD = 107;         // 悬赏问答
    const DOC = 108;            // 文件附件

    /**
     * @desc 异步执行更新帖子类型标签
     * @param ThreadTom $tom
     */
    public function updateThreadTag(ThreadTom $tom)
    {

    }

}
