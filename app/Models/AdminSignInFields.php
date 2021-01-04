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

namespace App\Models;


use Discuz\Models\DzqModel;
/**
 * @property int $id
 * @property string $name
 * @property int $type
 * @property string $fields_ext
 * @property string $fields_desc
 * @property int $sort
 * @property int $status
 */
class AdminSignInFields extends DzqModel
{

    protected $table = 'admin_sign_in_fields';

    const STATUS_UNACTIVE = -1;//未启用
    const STATUS_DELETE = 0; //删除
    const STATUS_ACTIVE = 1; //启用

    const TYPE_SINGLE_LINE = 0;
    const TYPE_MULTI_LINE = 1;
    const TYPE_RADIO = 2;
    const TYPE_CHECKBOX = 3;
    const TYPE_IMAGE_UPLOAD = 4;
    const TYPE_ZIP_UPLOAD = 5;

    private $typeDic = [
        self::TYPE_SINGLE_LINE => '单行文本框',
        self::TYPE_MULTI_LINE => '多行文本框',
        self::TYPE_RADIO => '单选',
        self::TYPE_CHECKBOX => '复选',
        self::TYPE_IMAGE_UPLOAD => '图片上传',
        self::TYPE_ZIP_UPLOAD => '附件上传'
    ];

    /**
     *获取全部扩展字段
     */
    public function getAdminSignInFields()
    {
        $ret = self::query()->select(['id', 'name', 'type', 'fields_ext', 'fields_desc','sort','status','required'])
            ->where('status', '<>',self::STATUS_DELETE)
            ->orderBy('sort', 'asc')
            ->get()->toArray();
        array_walk($ret, function (&$item) {
            $item['type_desc'] = $this->typeDic[$item['type']];
        });
        return $ret;
    }
    public function getActiveAdminSignInFields()
    {
        $ret = self::query()->select(['id', 'name', 'type', 'fields_ext', 'fields_desc','sort','status','required'])
            ->where('status', '=',self::STATUS_ACTIVE)
            ->orderBy('sort', 'asc')
            ->get()->toArray();
        return $ret;
    }
    public function getAdminSignIn($id){
        $r =  self::query()->where('id',$id)->first();
        if(empty($r)){
            return '';
        }else{
            return $r['type'];
        }

    }
}
