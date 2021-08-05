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

namespace App\Modules\ThreadTom;

class TomConfig
{

    const TOM_TEXT = 100;//文字内容，目前不单独作为扩展插件存储
    const TOM_IMAGE = 101;
    const TOM_AUDIO = 102;
    const TOM_VIDEO = 103;
    const TOM_GOODS = 104;
    const TOM_REDPACK = 106;
    const TOM_REWARD = 107;
    const TOM_DOC = 108;

    public static $map = [
        self::TOM_TEXT => [
            'enName' => 'TEXT',
            'desc' => '文字',
            'authorize'=>'',
            'service' => ''
        ],
        self::TOM_IMAGE => [
            'enName' => 'IMAGE',
            'desc' => '图片',
            'authorize'=>'switch.insertImage',
            'service' => \App\Modules\ThreadTom\Busi\ImageBusi::class
        ],
        self::TOM_AUDIO => [
            'enName' => 'AUDIO',
            'desc' => '语音',
            'authorize'=>'switch.insertAudio',
            'service' => \App\Modules\ThreadTom\Busi\AudioBusi::class
        ],
        self::TOM_VIDEO => [
            'enName' => 'VIDEO',
            'desc' => '视频',
            'authorize'=>'switch.insertVideo',
            'service' => \App\Modules\ThreadTom\Busi\VideoBusi::class
        ],
        self::TOM_GOODS => [
            'enName' => 'GOODS',
            'desc' => '商品',
            'authorize'=>'switch.insertGoods',
            'service' => \App\Modules\ThreadTom\Busi\GoodsBusi::class
        ],
//        self::TOM_QA => [
//            'enName' => 'QA',
//            'desc' => '问答',
//            'service' => \App\Modules\ThreadTom\Busi\QABusi::class
//        ],
        self::TOM_REDPACK => [
            'enName' => 'REDPACK',
            'desc' => '红包',
            'authorize'=>'switch.insertRedPacket',
            'service' => \App\Modules\ThreadTom\Busi\RedPackBusi::class
        ],
        self::TOM_REWARD => [
            'enName' => 'REWARD',
            'desc' => '悬赏',
            'authorize'=>'switch.insertReward',
            'service' => \App\Modules\ThreadTom\Busi\RewardBusi::class
        ],
        self::TOM_DOC => [
            'enName' => 'DOC',
            'desc' => '文件附件',
            'authorize'=>'switch.insertDoc',
            'service' => \App\Modules\ThreadTom\Busi\DocBusi::class
        ]
    ];

    //扩展属性
    const AUTHORIZE_PAY = 'switch.insertPay';
    const AUTHORIZE_POSITION = 'switch.insertPosition';


}
