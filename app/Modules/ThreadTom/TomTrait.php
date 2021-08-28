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


use App\Common\CacheKey;
use App\Models\Order;
use App\Models\OrderChildren;
use App\Models\ThreadRedPacket;
use App\Models\ThreadReward;
use Discuz\Base\DzqCache;
use App\Common\ResponseCode;
use App\Models\Permission;
use App\Models\ThreadTom;
use App\Models\User;
use Discuz\Base\DzqLog;
use Discuz\Common\Utils;
use Illuminate\Support\Arr;

trait TomTrait
{

    private $CREATE_FUNC = 'create';
    private $DELETE_FUNC = 'delete';
    private $UPDATE_FUNC = 'update';
    private $SELECT_FUNC = 'select';


    private $CLOSE_BUSI_PERMISSION = true;

    /**
     * @desc 支持一次提交包含新建或者更新或者删除等各种类型混合
     * @param $tomContent
     * @param null $operation
     * @param null $threadId
     * @param null $postId
     * @param bool $canViewTom
     * @return array
     */
    private function tomDispatcher($tomContent, $operation = null, $threadId = null, $postId = null, $canViewTom = true)
    {
        $config = TomConfig::$map;
        $tomJsons = [];
        if (isset($tomContent['indexes'])) {
            $indexes = $tomContent['indexes'];
        } else {
            if (isset($tomContent['text'])) {
                $indexes = [];
            } else {
                $indexes = $tomContent;
            }
        }
        if (empty($indexes)) return $tomJsons;
        $tomList = [];
        if (!empty($threadId) && empty($operation)) {
            $tomList = DzqCache::hGet(CacheKey::LIST_THREADS_V3_TOMS, $threadId, function ($threadId) {
                return ThreadTom::query()
                    ->select('tom_type', 'key')
                    ->where(['thread_id' => $threadId, 'status' => ThreadTom::STATUS_ACTIVE])->get()->toArray();
            });
        }
        foreach ($indexes as $key => $tomJson) {
            $this->setOperation($threadId, $operation, $key, $tomJson, $tomList);
            $this->busiPermission($this->user, $tomJson);
            if (isset($tomJson['tomId']) && isset($tomJson['operation'])) {
                if (in_array($tomJson['operation'], [$this->CREATE_FUNC, $this->DELETE_FUNC, $this->UPDATE_FUNC, $this->SELECT_FUNC])) {
                    $tomId = strval($tomJson['tomId']);
                    $op = $tomJson['operation'];
                    $body = $tomJson['body'] ?? false;
                    if (isset($config[$tomId])) {
                        $busiClass = $config[$tomId]['service'];
                    } else {
                        $busiClass = \App\Modules\ThreadTom\Busi\DefaultBusi::class;
                    }
                    $service = new \ReflectionClass($busiClass);
                    if (empty($tomJson['threadId'])) {
                        $service = $service->newInstanceArgs([$this->user, $threadId, $postId, $tomId, $key, $op, $body, $canViewTom]);
                    } else {
                        $service = $service->newInstanceArgs([$this->user, $tomJson['threadId'], $postId, $tomId, $key, $op, $body, $canViewTom]);
                    }
                    method_exists($service, $op) && $tomJsons[$key] = $service->$op();
//                    if (isset($config[$tomId])) {
//                        try {
//                            $service = new \ReflectionClass($config[$tomId]['service']);
//                            if (empty($tomJson['threadId'])) {
//                                $service = $service->newInstanceArgs([$this->user, $threadId, $postId, $tomId, $key, $op, $body, $canViewTom]);
//                            } else {
//                                $service = $service->newInstanceArgs([$this->user, $tomJson['threadId'], $postId, $tomId, $key, $op, $body, $canViewTom]);
//                            }
//                            method_exists($service, $op) && $tomJsons[$key] = $service->$op();
//                        } catch (\ReflectionException $e) {
//                            Utils::outPut(ResponseCode::INTERNAL_ERROR, $e->getMessage());
//                        }
//                    }
                }
            }
        }
        return $tomJsons;
    }

    /**
     * @desc 识别当前的操作类型
     * @param $threadId
     * @param $operation
     * @param $key
     * @param $tomJson
     * @param $tomList
     * @return mixed
     */
    private function setOperation($threadId, $operation, $key, &$tomJson, $tomList)
    {
        !empty($operation) && $tomJson['operation'] = $operation;
        if (!isset($tomJson['operation'])) {
            if (empty($tomJson['body'])) {
                $tomJson['operation'] = $this->DELETE_FUNC;
            } else {//create/update
                if (empty($threadId)) {
                    $tomJson['operation'] = $this->CREATE_FUNC;
                } else {
                    if (in_array(['tom_type' => $tomJson['tomId'], 'key' => $key], $tomList)) {
                        $tomJson['operation'] = $this->UPDATE_FUNC;
                    } else {
                        $tomJson['operation'] = $this->CREATE_FUNC;
                    }
                }
            }
        }
        return $tomJson;
    }

    private function busiPermission(User $user, $tom)
    {
        if ($this->CLOSE_BUSI_PERMISSION) {
            return true;
        }
        if ($user->isAdmin()) {
            return true;
        }
        if (!empty($tom['operation']) && $tom['operation'] == $this->CREATE_FUNC) {
            $tomConfig = TomConfig::$map[$tom['tomId']];
            $permissions = Permission::getUserPermissions($this->user);
            //todo 权限名称+分组id
            if (!in_array($tomConfig['authorize'], $permissions)) {
                Utils::outPut(ResponseCode::UNAUTHORIZED, sprintf('没有插入【%s】权限', $tomConfig['desc']));
            }
        }
        return true;
    }


    private function buildTomJson($threadId, $tomId, $operation, $body)
    {
        return [
            'threadId' => $threadId,
            'tomId' => $tomId,
            'operation' => $operation,
            'body' => $body
        ];
    }

    /**
     * @desc 创建新贴权限
     * @param User $user
     * @param $categoryId
     * @return bool
     */
    private function canCreateThread(User $user, $categoryId)
    {
        if ($user->isAdmin()) {
            return true;
        }
        $permissions = Permission::getUserPermissions($user);
        $permission = 'category' . $categoryId . '.createThread';
        if (in_array('createThread', $permissions) || in_array($permission, $permissions)) {
            return true;
        }
        return false;
    }

    /**
     * @desc 阅读帖子详情权限
     * @param $user
     * @param $thread
     * @return bool
     */
    private function canViewThreadDetail($user, $thread)
    {
        if ($user->isAdmin() || $user->id == $thread['user_id']) {
            return true;
        }
        $permissions = Permission::getUserPermissions($user);
        $permission = 'category' . $thread['category_id'] . '.thread.viewPosts';
        if (in_array('thread.viewPosts', $permissions) || in_array($permission, $permissions)) {
            return true;
        }
        return false;
    }

    /**
     * @desc 编辑更新帖子权限
     * @param User $user
     * @param $categoryId
     * @param null $threadUserId
     * @return bool
     */
    private function canEditThread(User $user, $categoryId, $threadUserId = null)
    {
        if ($user->isAdmin()) {
            return true;
        }
        $permissions = Permission::getUserPermissions($user);
        $permission = 'category' . $categoryId . '.thread.edit';
        if (in_array('thread.edit', $permissions) || in_array($permission, $permissions)) {
            return true;
        }
        if (!empty($threadUserId) && $user->id == $threadUserId) {
            $permission = 'category' . $categoryId . '.thread.editOwnThreadOrPost';
            if (in_array('thread.editOwnThreadOrPost', $permissions) || in_array($permission, $permissions)) {
                return true;
            }
        }
    }

    /**
     * @desc 删除帖子的权限
     * @param User $user
     * @param $categoryId
     * @param null $threadUserId
     * @return bool
     */
    private function canDeleteThread(User $user, $categoryId, $threadUserId = null)
    {
        if ($user->isAdmin()) {
            return true;
        }
        $permissions = Permission::getUserPermissions($user);
        $permission = 'category' . $categoryId . '.thread.hide';
        if (in_array('thread.hide', $permissions) || in_array($permission, $permissions)) {
            return true;
        }
        if (!empty($threadUserId) && $user->id == $threadUserId) {
            $permission = 'category' . $categoryId . '.thread.hideOwnThreadOrPost';
            if (in_array('thread.hideOwnThreadOrPost', $permissions) || in_array($permission, $permissions)) {
                return true;
            }
        }
        return false;
    }

    /**
     * 检测是否有需要支付的 tom
     *
     * @param array $tomJsons
     * @return bool
     */
    private function needPay($tomJsons)
    {
        if(empty($tomJsons))        return false;
        $tomTypes = array_keys($tomJsons);
        foreach ($tomTypes as $tomType) {
            $tomService = Arr::get(TomConfig::$map, $tomType . '.service');
            if (class_exists($tomService) && constant($tomService . '::NEED_PAY')) {
                return true;
            }else{
                DzqLog::info('service_not_exist', [$tomService, $tomJsons, $tomTypes]);
            }
        }
        return false;
    }


    /**
     * @param $threadId
     * @param bool $isDeleteRedOrder        删除红包相关数据
     * @param bool $isDeleteRewardOrder     删除悬赏相关数据
     */
    public function delRedRelations($threadId, $isDeleteRedOrder = false, $isDeleteRewardOrder = false){
        //将对应的 order、orderChildren、threadRedPacket、threadReward 与 原帖 脱离关系
        $order = self::getRedOrderInfo($threadId);
        if(empty($order) || $order->staus != Order::ORDER_STATUS_PAID){         //订单未支付的情况下才删除数据
            if($isDeleteRedOrder){      //删除之前的order、orderChildren、$threadRedPacket
                Order::query()->where('thread_id', $threadId)->update(['thread_id' => 0]);
                if($order->type == Order::ORDER_TYPE_MERGE){
                    OrderChildren::query()->where(['order_sn' => $order->order_sn, 'thread_id' => $threadId])->update(['thread_id' => 0]);
                }
                ThreadRedPacket::query()->where(['thread_id' => $threadId])->update(['thread_id' => 0, 'post_id' => 0]);
            }
            if($isDeleteRewardOrder){
                Order::query()->where('thread_id', $threadId)->update(['thread_id' => 0]);
                if($order->type == Order::ORDER_TYPE_MERGE){
                    OrderChildren::query()->where(['order_sn' => $order->order_sn, 'thread_id' => $threadId])->update(['thread_id' => 0]);
                }
                ThreadReward::query()->where(['thread_id' => $threadId])->update(['thread_id' => 0, 'post_id' => 0]);
            }
        }
    }

    public function getRedOrderInfo($threadId){
        return  Order::query()->where('thread_id', $threadId)
            ->whereIn('type', [Order::ORDER_TYPE_REDPACKET, Order::ORDER_TYPE_QUESTION_REWARD, Order::ORDER_TYPE_MERGE])
            ->first();
    }
}
