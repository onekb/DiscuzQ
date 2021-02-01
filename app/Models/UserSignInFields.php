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

use Discuz\Auth\Exception\PermissionDeniedException;
use Discuz\Contracts\Setting\SettingsRepository;
use Discuz\Models\DzqModel;

/**
 * @property int $id
 * @property int $aid
 * @property int $user_id
 * @property string $fields_ext
 * @property string $remark
 * @property int $status
 * */
class UserSignInFields extends DzqModel
{
    protected $table = 'user_sign_in_fields';

    const STATUS_DELETE = 0; //废弃
    const STATUS_AUDIT = 1;//待审核
    const STATUS_REJECT = 2;//已驳回
    const STATUS_PASS = 3;//审核通过

    public function getUserSignInFields($userId)
    {
        if (empty($userId)) throw new PermissionDeniedException('用户id错误');
        $extList = self::query()
            ->select(['id', 'user_id', 'name', 'type', 'fields_ext', 'fields_desc', 'remark', 'sort', 'status', 'required'])
            ->where('user_id', $userId)
            ->where('status', '!=', self::STATUS_DELETE)
            ->orderBy('sort', 'asc')
            ->get()->toArray();
        if (empty($extList)) {
            $extList = AdminSignInFields::instance()->getActiveAdminSignInFields();
            array_walk($extList, function (&$item) {
                $item['id'] = '';
            });
        }
        return $extList;
    }

    /**
     *用户新建或编辑扩展字段内容
     * @param $userId
     * @param $attributes
     * @return array
     */
    public function userSaveUserSignInFields($userId, $attributes)
    {
        if (empty($userId)) throw new PermissionDeniedException('用户id错误');
        $settings = app(SettingsRepository::class);
        $register_validate = $settings->get('register_validate');
        if ($register_validate) {
            if (User::isStatusMod($userId)) {
                throw new PermissionDeniedException('register_validate');
            }
        }
        $data = [];
        foreach ($attributes as $attribute) {
            if (!empty($attribute['id'])) {//更新
                $userSignIn = self::query()->where('id', $attribute['id'])
                    ->where('status', '!=', self::STATUS_DELETE)
                    ->where('user_id', $userId)
                    ->first();
                if (empty($userSignIn)) {
                    continue;
                }
                if ($userSignIn['status'] == self::STATUS_REJECT) {
                    $userSignIn->setAttribute('status', self::STATUS_DELETE);
                    $userSignIn->save();
                    $userSignIn = new UserSignInFields();
                }
            } else {//新建
                $userSignIn = new UserSignInFields();
            }
            $rawData = [
                'user_id' => $userId,
                'name' => $attribute['name'],
                'type' => $attribute['type'],
                'fields_ext' => $attribute['fields_ext'],
                'fields_desc' => $attribute['fields_desc'],
                'sort' => $attribute['sort'],
                'status' => self::STATUS_AUDIT,
                'required' => $attribute['required']
            ];
            $userSignIn->setRawAttributes($rawData);
            $userSignIn->save();
            $data[] = $userSignIn;
        }
        //修改user的status为2，待审核状态
        if ($register_validate) {
            User::setUserStatusMod($userId);
        } else {
            User::setUserStatusNormal($userId);
        }
        return $data;
    }

    /**
     *管理员审核扩展信息
     * @param $userId
     * @param $attributes
     * @return array
     */
    public function adminSaveUserSignInFields($userId, $attributes)
    {
        if (empty($userId)) throw new PermissionDeniedException('用户id错误');
        $isAuditPass = true;
        $data = [];
        foreach ($attributes as $attribute) {
            $userSignIn = self::query()->where('id', $attribute['id'])
                ->where('user_id', $userId)
                ->first();
            if (empty($userSignIn)) {
                continue;
            }
            $rawData = [
                'user_id' => $userId,
                'fields_ext' => $attribute['fields_ext'],
                'remark' => $attribute['remark'],
                'status' => $attribute['status'],
            ];
            $attribute['status'] != self::STATUS_PASS && $isAuditPass = false;
            $userSignIn->setRawAttributes($rawData);
            if (!$userSignIn->save()) {
                $isAuditPass = false;
            }
            $data[] = $userSignIn;
        }
        if ($isAuditPass) {
            $user = User::query()->where('id', $userId)->get()->first();
            $user->status = User::STATUS_NORMAL;
            $user->save();
        }
        return $data;
    }

    /**
     *查询用户填写的扩展字段信息
     * @param $userId
     * @return array
     */
    public function getUserRecordFields($userId)
    {
        return self::query()
            ->where('user_id', $userId)
            ->where('status', '!=', self::STATUS_DELETE)
            ->get()->all();
    }

    public function getUserRejectReason($userId)
    {
        $data = self::query()->where(['user_id' => $userId, 'status' => self::STATUS_REJECT])->get();
        if ($data->isEmpty()) {
            return false;
        }
        $reason = [];
        $data->each(function (UserSignInFields $item) use (&$reason) {
            $remark = $item->remark;
            !in_array($remark, $reason) && $reason[] = $remark;
        });
        return $reason;
    }

}
