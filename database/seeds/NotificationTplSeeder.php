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

use App\Models\NotificationTpl;
use App\Notifications\Messages\TemplateVariables;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;

class NotificationTplSeeder extends Seeder
{
    use TemplateVariables;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $notificationTpl = new NotificationTpl();
        $notificationTpl->truncate();

        // 获取系统通知
        $system = $this->systemData();
        $notificationTpl->insert($system);

        // 添加微信通知
        $wechat = $this->getFilterTpl(false, ['wechat', 'miniProgram']);
        $notificationTpl->insert($wechat);

        /**
         * 注意：由于数据格式不一致，分开 insert 执行
         */
        $simplified = $this->getFilterTpl(false, ['sms']);
        $notificationTpl->insert($simplified);
    }

    /**
     * 获取所有模板数据
     *
     * @param false $isCollect 是否返回集合数据
     * @param array $filter 指定获取某种数据
     * @return array|Collection
     */
    public function getFilterTpl($isCollect = false, $filter = [])
    {
        $merge = [];

        if (! empty($filter)) {
            $merge = in_array('system', $filter) ? array_merge($merge, $this->systemData()) : $merge;
            $merge = in_array('wechat', $filter) ? array_merge($merge, $this->wechatData()) : $merge;
            $merge = in_array('sms', $filter) ? array_merge($merge, $this->sms()) : $merge;
            $merge = in_array('miniProgram', $filter) ? array_merge($merge, $this->miniProgram()) : $merge;
        } else {
            $merge = array_merge($this->systemData(), $this->wechatData(), $this->sms(), $this->miniProgram());
        }

        return $isCollect ? collect($merge) : $merge;
    }

    /**
     * 微信通知
     *
     * @return array
     */
    public function wechatData()
    {
        return [
            [
                'status'    => 0,
                'type'      => 1,
                'notice_id' => $this->comparisonUnique('新用户注册通知', 1),
                'type_name' => '新用户注册通知',
                'title'     => '微信注册通知',
                'page_path' => $this->getInitPagePath($this->comparisonUnique('新用户注册通知', 1)) ?? '',
            ],
            [
                'status'    => 0,
                'type'      => 1,
                'notice_id' => $this->comparisonUnique('注册审核通过通知', 1),
                'type_name' => '注册审核通过通知',
                'title'     => '微信注册审核通知',
                'page_path' => $this->getInitPagePath($this->comparisonUnique('注册审核通过通知', 1)) ?? '',
            ],
            [
                'status'    => 0,
                'type'      => 1,
                'notice_id' => $this->comparisonUnique('注册审核不通过通知', 1),
                'type_name' => '注册审核不通过通知',
                'title'     => '微信注册审核通知',
                'page_path' => $this->getInitPagePath($this->comparisonUnique('注册审核不通过通知', 1)) ?? '',
            ],
            [
                'status'    => 0,
                'type'      => 1,
                'notice_id' => $this->comparisonUnique('内容审核通过通知', 1),
                'type_name' => '内容审核通过通知',
                'title'     => '微信内容审核通知',
                'page_path' => $this->getInitPagePath($this->comparisonUnique('内容审核通过通知', 1)) ?? '',
            ],
            [
                'status'    => 0,
                'type'      => 1,
                'notice_id' => $this->comparisonUnique('内容审核不通过通知', 1),
                'type_name' => '内容审核不通过通知',
                'title'     => '微信内容审核通知',
                'page_path' => $this->getInitPagePath($this->comparisonUnique('内容审核不通过通知', 1)) ?? '',
            ],
            [
                'status'    => 0,
                'type'      => 1,
                'notice_id' => $this->comparisonUnique('内容删除通知', 1),
                'type_name' => '内容删除通知',
                'title'     => '微信内容通知',
                'page_path' => $this->getInitPagePath($this->comparisonUnique('内容删除通知', 1)) ?? '',
            ],
            [
                'status'    => 0,
                'type'      => 1,
                'notice_id' => $this->comparisonUnique('内容精华通知', 1),
                'type_name' => '内容精华通知',
                'title'     => '微信内容通知',
                'page_path' => $this->getInitPagePath($this->comparisonUnique('内容精华通知', 1)) ?? '',
            ],
            [
                'status'    => 0,
                'type'      => 1,
                'notice_id' => $this->comparisonUnique('内容置顶通知', 1),
                'type_name' => '内容置顶通知',
                'title'     => '微信内容通知',
                'page_path' => $this->getInitPagePath($this->comparisonUnique('内容置顶通知', 1)) ?? '',
            ],
            [
                'status'    => 0,
                'type'      => 1,
                'notice_id' => $this->comparisonUnique('内容修改通知', 1),
                'type_name' => '内容修改通知',
                'title'     => '微信内容通知',
                'page_path' => $this->getInitPagePath($this->comparisonUnique('内容修改通知', 1)) ?? '',
            ],
            [
                'status'    => 0,
                'type'      => 1,
                'notice_id' => $this->comparisonUnique('用户禁用通知', 1),
                'type_name' => '用户禁用通知',
                'title'     => '微信用户通知',
                'page_path' => $this->getInitPagePath($this->comparisonUnique('用户禁用通知', 1)) ?? '',
            ],
            [
                'status'    => 0,
                'type'      => 1,
                'notice_id' => $this->comparisonUnique('用户解除禁用通知', 1),
                'type_name' => '用户解除禁用通知',
                'title'     => '微信用户通知',
                'page_path' => $this->getInitPagePath($this->comparisonUnique('用户解除禁用通知', 1)) ?? '',
            ],
            [
                'status'    => 0,
                'type'      => 1,
                'notice_id' => $this->comparisonUnique('用户角色调整通知', 1),
                'type_name' => '用户角色调整通知',
                'title'     => '微信角色通知',
                'page_path' => $this->getInitPagePath($this->comparisonUnique('用户角色调整通知', 1)) ?? '',
            ],
            [
                'status'    => 0,
                'type'      => 1,
                'notice_id' => $this->comparisonUnique('内容回复通知', 1),
                'type_name' => '内容回复通知',
                'title'     => '微信内容通知',
                'page_path' => $this->getInitPagePath($this->comparisonUnique('内容回复通知', 1)) ?? '',
            ],
            [
                'status'    => 0,
                'type'      => 1,
                'notice_id' => $this->comparisonUnique('内容点赞通知', 1),
                'type_name' => '内容点赞通知',
                'title'     => '微信内容通知',
                'page_path' => $this->getInitPagePath($this->comparisonUnique('内容点赞通知', 1)) ?? '',
            ],
            [
                'status'    => 0,
                'type'      => 1,
                'notice_id' => $this->comparisonUnique('内容支付通知', 1),
                'type_name' => '内容支付通知',
                'title'     => '微信内容通知',
                'page_path' => $this->getInitPagePath($this->comparisonUnique('内容支付通知', 1)) ?? '',
            ],
            [
                'status'    => 0,
                'type'      => 1,
                'notice_id' => $this->comparisonUnique('内容@通知', 1),
                'type_name' => '内容@通知',
                'title'     => '微信内容通知',
                'page_path' => $this->getInitPagePath($this->comparisonUnique('内容@通知', 1)) ?? '',
            ],
            [
                'status'    => 0,
                'type'      => 1,
                'notice_id' => $this->comparisonUnique('提现通知', 1),
                'type_name' => '提现通知',
                'title'     => '微信财务通知',
                'page_path' => $this->getInitPagePath($this->comparisonUnique('提现通知', 1)) ?? '',
            ],
            [
                'status'    => 0,
                'type'      => 1,
                'notice_id' => $this->comparisonUnique('提现失败通知', 1),
                'type_name' => '提现失败通知',
                'title'     => '微信财务通知',
                'page_path' => $this->getInitPagePath($this->comparisonUnique('提现失败通知', 1)) ?? '',
            ],
            [
                'status'    => 0,
                'type'      => 1,
                'notice_id' => $this->comparisonUnique('分成收入通知', 1),
                'type_name' => '分成收入通知',
                'title'     => '微信内容通知',
                'page_path' => $this->getInitPagePath($this->comparisonUnique('分成收入通知', 1)) ?? '',
            ],
            [
                'status'    => 0,
                'type'      => 1,
                'notice_id' => $this->comparisonUnique('问答提问通知', 1),
                'type_name' => '问答提问通知',
                'title'     => '微信问答通知',
                'page_path' => $this->getInitPagePath($this->comparisonUnique('问答提问通知', 1)) ?? '',
            ],
            [
                'status'    => 0,
                'type'      => 1,
                'notice_id' => $this->comparisonUnique('问答回答通知', 1),
                'type_name' => '问答回答通知',
                'title'     => '微信问答通知',
                'page_path' => $this->getInitPagePath($this->comparisonUnique('问答回答通知', 1)) ?? '',
            ],
            [
                'status'    => 0,
                'type'      => 1,
                'notice_id' => $this->comparisonUnique('问答过期通知', 1),
                'type_name' => '问答过期通知',
                'title'     => '微信问答通知',
                'page_path' => $this->getInitPagePath($this->comparisonUnique('问答过期通知', 1)) ?? '',
            ],
            [
                'status'    => 0,
                'type'      => 1,
                'notice_id' => $this->comparisonUnique('得到红包通知', 1),
                'type_name' => '得到红包通知',
                'title'     => '微信红包通知',
                'page_path' => $this->getInitPagePath($this->comparisonUnique('得到红包通知', 1)) ?? '',
            ],
            [
                'status'    => 0,
                'type'      => 1,
                'notice_id' => $this->comparisonUnique('悬赏问答通知', 1),
                'type_name' => '悬赏问答通知',
                'title'     => '微信悬赏通知',
                'page_path' => $this->getInitPagePath($this->comparisonUnique('悬赏问答通知', 1)) ?? '',
            ],
            [
                'status'    => 0,
                'type'      => 1,
                'notice_id' => $this->comparisonUnique('悬赏过期通知', 1),
                'type_name' => '悬赏过期通知',
                'title'     => '微信悬赏通知',
                'page_path' => $this->getInitPagePath($this->comparisonUnique('悬赏过期通知', 1)) ?? '',
            ],
        ];
    }

    /**
     * 系统通知
     *
     * @return array
     */
    public function systemData()
    {
        return [
            [
                'status'    => 1,
                'type'      => 0,
                'notice_id' => $this->comparisonUnique('新用户注册通知', 0),
                'type_name' => '新用户注册通知',
                'title'     => '欢迎加入{sitename}',
                'content'   => '{username}你好，你已经成为{sitename} 的{groupname} ，请你在发表言论时，遵守当地法律法规。祝你在这里玩的愉快。',
                'vars'      => serialize([
                    '{username}'  => '用户名',
                    '{sitename}'  => '站点名称',
                    '{groupname}' => '用户组',
                ]),
            ],
            [
                'status'    => 1,
                'type'      => 0,
                'notice_id' => $this->comparisonUnique('注册审核通过通知', 0),
                'type_name' => '注册审核通过通知',
                'title'     => '注册审核通知',
                'content'   => '{username}你好，你的注册申请已审核通过。',
                'vars'      => serialize([
                    '{username}' => '用户名',
                ]),
            ],
            [
                'status'    => 1,
                'type'      => 0,
                'notice_id' => $this->comparisonUnique('注册审核不通过通知', 0),
                'type_name' => '注册审核不通过通知',
                'title'     => '注册审核通知',
                'content'   => '{username}你好，你的注册申请审核不通过，原因：{reason}',
                'vars'      => serialize([
                    '{username}' => '用户名',
                    '{reason}'   => '原因',
                ]),
            ],
            [
                'status'    => 1,
                'type'      => 0,
                'notice_id' => $this->comparisonUnique('内容审核不通过通知', 0),
                'type_name' => '内容审核不通过通知',
                'title'     => '内容审核通知',
                'content'   => '{username}你好，你发布的内容 "{content}" 审核不通过，原因：{reason}',
                'vars'      => serialize([
                    '{username}' => '用户名',
                    '{content}'  => '内容',
                    '{reason}'   => '原因',
                ]),
            ],
            [
                'status'    => 1,
                'type'      => 0,
                'notice_id' => $this->comparisonUnique('内容审核通过通知', 0),
                'type_name' => '内容审核通过通知',
                'title'     => '内容审核通知',
                'content'   => '{username}你好，你发布的内容 "{content}" 审核通过',
                'vars'      => serialize([
                    '{username}' => '用户名',
                    '{content}'  => '内容',
                ]),
            ],
            [
                'status'    => 1,
                'type'      => 0,
                'notice_id' => $this->comparisonUnique('内容删除通知', 0),
                'type_name' => '内容删除通知',
                'title'     => '内容通知',
                'content'   => '{username}你好，你发布的内容 "{content} " 已删除，原因：{reason}',
                'vars'      => serialize([
                    '{username}' => '用户名',
                    '{content}'  => '内容',
                    '{reason}'   => '原因',
                ]),
            ],
            [
                'status'    => 1,
                'type'      => 0,
                'notice_id' => $this->comparisonUnique('内容精华通知', 0),
                'type_name' => '内容精华通知',
                'title'     => '内容通知',
                'content'   => '{username}你好，你发布的内容 "{content}" 已设为精华',
                'vars'      => serialize([
                    '{username}' => '用户名',
                    '{content}'  => '内容',
                ]),
            ],
            [
                'status'    => 1,
                'type'      => 0,
                'notice_id' => $this->comparisonUnique('内容置顶通知', 0),
                'type_name' => '内容置顶通知',
                'title'     => '内容通知',
                'content'   => '{username}你好，你发布的内容 "{content}" 已置顶',
                'vars'      => serialize([
                    '{username}' => '用户名',
                    '{content}'  => '内容',
                ]),
            ],
            [
                'status'    => 1,
                'type'      => 0,
                'notice_id' => $this->comparisonUnique('内容修改通知', 0),
                'type_name' => '内容修改通知',
                'title'     => '内容通知',
                'content'   => '{username}你好，你发布的内容 "{content}" 已被修改',
                'vars'      => serialize([
                    '{username}' => '用户名',
                    '{content}'  => '内容',
                ]),
            ],
            [
                'status'    => 1,
                'type'      => 0,
                'notice_id' => $this->comparisonUnique('用户禁用通知', 0),
                'type_name' => '用户禁用通知',
                'title'     => '用户通知',
                'content'   => '{username}你好，你的帐号已禁用，原因：{reason}',
                'vars'      => serialize([
                    '{username}' => '用户名',
                    '{reason}'   => '原因',
                ]),
            ],
            [
                'status'    => 1,
                'type'      => 0,
                'notice_id' => $this->comparisonUnique('用户解除禁用通知', 0),
                'type_name' => '用户解除禁用通知',
                'title'     => '用户通知',
                'content'   => '{username}你好，你的帐号已解除禁用',
                'vars'      => serialize([
                    '{username}' => '用户名',
                ]),
            ],
            [
                'status'    => 1,
                'type'      => 0,
                'notice_id' => $this->comparisonUnique('用户角色调整通知', 0),
                'type_name' => '用户角色调整通知',
                'title'     => '角色通知',
                'content'   => '{username}你好，你的角色由{oldgroupname}变更为{newgroupname}',
                'vars'      => serialize([
                    '{username}'     => '用户名',
                    '{oldgroupname}' => '老用户组',
                    '{newgroupname}' => '新用户组',
                ]),
            ],
            [
                'status'    => 1,
                'type'      => 0,
                'notice_id' => $this->comparisonUnique('内容回复通知', 0),
                'type_name' => '内容回复通知',
                'title'     => '内容通知',
                'content'   => '',
                'vars'      => '',
            ],
            [
                'status'    => 1,
                'type'      => 0,
                'notice_id' => $this->comparisonUnique('内容点赞通知', 0),
                'type_name' => '内容点赞通知',
                'title'     => '内容通知',
                'content'   => '',
                'vars'      => '',
            ],
            [
                'status'    => 1,
                'type'      => 0,
                'notice_id' => $this->comparisonUnique('内容支付通知', 0),
                'type_name' => '内容支付通知',
                'title'     => '内容通知',
                'content'   => '',
                'vars'      => '',
            ],
            [
                'status'    => 1,
                'type'      => 0,
                'notice_id' => $this->comparisonUnique('内容@通知', 0),
                'type_name' => '内容@通知',
                'title'     => '内容通知',
                'content'   => '',
                'vars'      => '',
            ],
            [
                'status'    => 1,
                'type'      => 0,
                'notice_id' => $this->comparisonUnique('提现通知', 0),
                'type_name' => '提现通知',
                'title'     => '财务通知',
                'content'   => '',
                'vars'      => '',
            ],
            [
                'status'    => 1,
                'type'      => 0,
                'notice_id' => $this->comparisonUnique('提现失败通知', 0),
                'type_name' => '提现失败通知',
                'title'     => '财务通知',
                'content'   => '',
                'vars'      => '',
            ],
            [
                'status'    => 1,
                'type'      => 0,
                'notice_id' => $this->comparisonUnique('分成收入通知', 0),
                'type_name' => '分成收入通知',
                'title'     => '内容通知',
                'content'   => '',
                'vars'      => '',
            ],
            [
                'status'    => 1,
                'type'      => 0,
                'notice_id' => $this->comparisonUnique('问答提问通知', 0),
                'type_name' => '问答提问通知',
                'title'     => '问答通知',
                'content'   => '',
                'vars'      => '',
            ],
            [
                'status'    => 1,
                'type'      => 0,
                'notice_id' => $this->comparisonUnique('问答回答通知', 0),
                'type_name' => '问答回答通知',
                'title'     => '问答通知',
                'content'   => '',
                'vars'      => '',
            ],
            [
                'status'    => 1,
                'type'      => 0,
                'notice_id' => $this->comparisonUnique('问答过期通知', 0),
                'type_name' => '问答过期通知',
                'title'     => '内容通知',
                'content'   => '',
                'vars'      => '',
            ],
            [
                'status'    => 1,
                'type'      => 0,
                'notice_id' => $this->comparisonUnique('得到红包通知', 0),
                'type_name' => '得到红包通知',
                'title'     => '红包通知',
                'content'   => '',
                'vars'      => '',
            ],
            [
                'status'    => 1,
                'type'      => 0,
                'notice_id' => $this->comparisonUnique('悬赏问答通知', 0),
                'type_name' => '悬赏问答通知',
                'title'     => '悬赏通知',
                'content'   => '',
                'vars'      => '',
            ],
            [
                'status'    => 1,
                'type'      => 0,
                'notice_id' => $this->comparisonUnique('悬赏过期通知', 0),
                'type_name' => '悬赏过期通知',
                'title'     => '悬赏通知',
                'content'   => '',
                'vars'      => '',
            ],
            [
                'status'    => 1,
                'type'      => 0,
                'notice_id' => $this->comparisonUnique('异常订单退款通知', 0),
                'type_name' => '异常订单退款通知',
                'title'     => '异常订单退款通知',
                'content'   => '',
                'vars'      => '',
            ]
        ];
    }

    /**
     * 短信通知
     *
     * @return array[]
     */
    public function sms()
    {
        return [
            [
                'status'    => 0,
                'type'      => 2,
                'notice_id' => $this->comparisonUnique('新用户注册通知', 2),
                'type_name' => '新用户注册通知',
                'title'     => '短信注册通知',
            ],
            [
                'status'    => 0,
                'type'      => 2,
                'notice_id' => $this->comparisonUnique('注册审核通过通知', 2),
                'type_name' => '注册审核通过通知',
                'title'     => '短信注册审核通知',
            ],
            [
                'status'    => 0,
                'type'      => 2,
                'notice_id' => $this->comparisonUnique('注册审核不通过通知', 2),
                'type_name' => '注册审核不通过通知',
                'title'     => '短信注册审核通知',
            ],
            [
                'status'    => 0,
                'type'      => 2,
                'notice_id' => $this->comparisonUnique('内容审核通过通知', 2),
                'type_name' => '内容审核通过通知',
                'title'     => '短信内容审核通知',
            ],
            [
                'status'    => 0,
                'type'      => 2,
                'notice_id' => $this->comparisonUnique('内容审核不通过通知', 2),
                'type_name' => '内容审核不通过通知',
                'title'     => '短信内容审核通知',
            ],
            [
                'status'    => 0,
                'type'      => 2,
                'notice_id' => $this->comparisonUnique('内容删除通知', 2),
                'type_name' => '内容删除通知',
                'title'     => '短信内容通知',
            ],
            [
                'status'    => 0,
                'type'      => 2,
                'notice_id' => $this->comparisonUnique('内容精华通知', 2),
                'type_name' => '内容精华通知',
                'title'     => '短信内容通知',
            ],
            [
                'status'    => 0,
                'type'      => 2,
                'notice_id' => $this->comparisonUnique('内容置顶通知', 2),
                'type_name' => '内容置顶通知',
                'title'     => '短信内容通知',
            ],
            [
                'status'    => 0,
                'type'      => 2,
                'notice_id' => $this->comparisonUnique('内容修改通知', 2),
                'type_name' => '内容修改通知',
                'title'     => '短信内容通知',
            ],
            [
                'status'    => 0,
                'type'      => 2,
                'notice_id' => $this->comparisonUnique('用户禁用通知', 2),
                'type_name' => '用户禁用通知',
                'title'     => '短信用户通知',
            ],
            [
                'status'    => 0,
                'type'      => 2,
                'notice_id' => $this->comparisonUnique('用户解除禁用通知', 2),
                'type_name' => '用户解除禁用通知',
                'title'     => '短信用户通知',
            ],
            [
                'status'    => 0,
                'type'      => 2,
                'notice_id' => $this->comparisonUnique('用户角色调整通知', 2),
                'type_name' => '用户角色调整通知',
                'title'     => '短信角色通知',
            ],
            [
                'status'    => 0,
                'type'      => 2,
                'notice_id' => $this->comparisonUnique('内容回复通知', 2),
                'type_name' => '内容回复通知',
                'title'     => '短信内容通知',
            ],
            [
                'status'    => 0,
                'type'      => 2,
                'notice_id' => $this->comparisonUnique('内容点赞通知', 2),
                'type_name' => '内容点赞通知',
                'title'     => '短信内容通知',
            ],
            [
                'status'    => 0,
                'type'      => 2,
                'notice_id' => $this->comparisonUnique('内容支付通知', 2),
                'type_name' => '内容支付通知',
                'title'     => '短信内容通知',
            ],
            [
                'status'    => 0,
                'type'      => 2,
                'notice_id' => $this->comparisonUnique('内容@通知', 2),
                'type_name' => '内容@通知',
                'title'     => '短信内容通知',
            ],
            [
                'status'    => 0,
                'type'      => 2,
                'notice_id' => $this->comparisonUnique('提现通知', 2),
                'type_name' => '提现通知',
                'title'     => '短信财务通知',
            ],
            [
                'status'    => 0,
                'type'      => 2,
                'notice_id' => $this->comparisonUnique('提现失败通知', 2),
                'type_name' => '提现失败通知',
                'title'     => '短信财务通知',
            ],
            [
                'status'    => 0,
                'type'      => 2,
                'notice_id' => $this->comparisonUnique('分成收入通知', 2),
                'type_name' => '分成收入通知',
                'title'     => '短信内容通知',
            ],
            [
                'status'    => 0,
                'type'      => 2,
                'notice_id' => $this->comparisonUnique('问答提问通知', 2),
                'type_name' => '问答提问通知',
                'title'     => '短信问答通知',
            ],
            [
                'status'    => 0,
                'type'      => 2,
                'notice_id' => $this->comparisonUnique('问答回答通知', 2),
                'type_name' => '问答回答通知',
                'title'     => '短信问答通知',
            ],
            [
                'status'    => 0,
                'type'      => 2,
                'notice_id' => $this->comparisonUnique('问答过期通知', 2),
                'type_name' => '问答过期通知',
                'title'     => '短信问答通知',
            ],
            [
                'status'    => 0,
                'type'      => 2,
                'notice_id' => $this->comparisonUnique('得到红包通知', 2),
                'type_name' => '得到红包通知',
                'title'     => '短信红包通知',
            ],
            [
                'status'    => 0,
                'type'      => 2,
                'notice_id' => $this->comparisonUnique('悬赏问答通知', 2),
                'type_name' => '悬赏问答通知',
                'title'     => '短信悬赏通知',
            ],
            [
                'status'    => 0,
                'type'      => 2,
                'notice_id' => $this->comparisonUnique('悬赏过期通知', 2),
                'type_name' => '悬赏过期通知',
                'title'     => '短信悬赏通知',
            ],
        ];
    }

    /**
     * 小程序通知
     *
     * @return array[]
     */
    public function miniProgram()
    {
        return [
            [
                'status'    => 0,
                'type'      => 4,
                'notice_id' => $this->comparisonUnique('新用户注册通知', 4),
                'type_name' => '新用户注册通知',
                'title'     => '小程序注册通知',
                'page_path' => $this->getInitPagePath($this->comparisonUnique('新用户注册通知', 4)) ?? '',
            ],
            [
                'status'    => 0,
                'type'      => 4,
                'notice_id' => $this->comparisonUnique('注册审核通过通知', 4),
                'type_name' => '注册审核通过通知',
                'title'     => '小程序注册审核通知',
                'page_path' => $this->getInitPagePath($this->comparisonUnique('注册审核通过通知', 4)) ?? '',
            ],
            [
                'status'    => 0,
                'type'      => 4,
                'notice_id' => $this->comparisonUnique('注册审核不通过通知', 4),
                'type_name' => '注册审核不通过通知',
                'title'     => '小程序注册审核通知',
                'page_path' => $this->getInitPagePath($this->comparisonUnique('注册审核不通过通知', 4)) ?? '',
            ],
            [
                'status'    => 0,
                'type'      => 4,
                'notice_id' => $this->comparisonUnique('内容审核通过通知', 4),
                'type_name' => '内容审核通过通知',
                'title'     => '小程序内容审核通知',
                'page_path' => $this->getInitPagePath($this->comparisonUnique('内容审核通过通知', 4)) ?? '',
            ],
            [
                'status'    => 0,
                'type'      => 4,
                'notice_id' => $this->comparisonUnique('内容审核不通过通知', 4),
                'type_name' => '内容审核不通过通知',
                'title'     => '小程序内容审核通知',
                'page_path' => $this->getInitPagePath($this->comparisonUnique('内容审核不通过通知', 4)) ?? '',
            ],
            [
                'status'    => 0,
                'type'      => 4,
                'notice_id' => $this->comparisonUnique('内容删除通知', 4),
                'type_name' => '内容删除通知',
                'title'     => '小程序内容通知',
                'page_path' => $this->getInitPagePath($this->comparisonUnique('内容删除通知', 4)) ?? '',
            ],
            [
                'status'    => 0,
                'type'      => 4,
                'notice_id' => $this->comparisonUnique('内容精华通知', 4),
                'type_name' => '内容精华通知',
                'title'     => '小程序内容通知',
                'page_path' => $this->getInitPagePath($this->comparisonUnique('内容精华通知', 4)) ?? '',
            ],
            [
                'status'    => 0,
                'type'      => 4,
                'notice_id' => $this->comparisonUnique('内容置顶通知', 4),
                'type_name' => '内容置顶通知',
                'title'     => '小程序内容通知',
                'page_path' => $this->getInitPagePath($this->comparisonUnique('内容置顶通知', 4)) ?? '',
            ],
            [
                'status'    => 0,
                'type'      => 4,
                'notice_id' => $this->comparisonUnique('内容修改通知', 4),
                'type_name' => '内容修改通知',
                'title'     => '小程序内容通知',
                'page_path' => $this->getInitPagePath($this->comparisonUnique('内容修改通知', 4)) ?? '',
            ],
            [
                'status'    => 0,
                'type'      => 4,
                'notice_id' => $this->comparisonUnique('用户禁用通知', 4),
                'type_name' => '用户禁用通知',
                'title'     => '小程序用户通知',
                'page_path' => $this->getInitPagePath($this->comparisonUnique('用户禁用通知', 4)) ?? '',
            ],
            [
                'status'    => 0,
                'type'      => 4,
                'notice_id' => $this->comparisonUnique('用户解除禁用通知', 4),
                'type_name' => '用户解除禁用通知',
                'title'     => '小程序用户通知',
                'page_path' => $this->getInitPagePath($this->comparisonUnique('用户解除禁用通知', 4)) ?? '',
            ],
            [
                'status'    => 0,
                'type'      => 4,
                'notice_id' => $this->comparisonUnique('用户角色调整通知', 4),
                'type_name' => '用户角色调整通知',
                'title'     => '小程序角色通知',
                'page_path' => $this->getInitPagePath($this->comparisonUnique('用户角色调整通知', 4)) ?? '',
            ],
            [
                'status'    => 0,
                'type'      => 4,
                'notice_id' => $this->comparisonUnique('内容回复通知', 4),
                'type_name' => '内容回复通知',
                'title'     => '小程序内容通知',
                'page_path' => $this->getInitPagePath($this->comparisonUnique('内容回复通知', 4)) ?? '',
            ],
            [
                'status'    => 0,
                'type'      => 4,
                'notice_id' => $this->comparisonUnique('内容点赞通知', 4),
                'type_name' => '内容点赞通知',
                'title'     => '小程序内容通知',
                'page_path' => $this->getInitPagePath($this->comparisonUnique('内容点赞通知', 4)) ?? '',
            ],
            [
                'status'    => 0,
                'type'      => 4,
                'notice_id' => $this->comparisonUnique('内容支付通知', 4),
                'type_name' => '内容支付通知',
                'title'     => '小程序内容通知',
                'page_path' => $this->getInitPagePath($this->comparisonUnique('内容支付通知', 4)) ?? '',
            ],
            [
                'status'    => 0,
                'type'      => 4,
                'notice_id' => $this->comparisonUnique('内容@通知', 4),
                'type_name' => '内容@通知',
                'title'     => '小程序内容通知',
                'page_path' => $this->getInitPagePath($this->comparisonUnique('内容@通知', 4)) ?? '',
            ],
            [
                'status'    => 0,
                'type'      => 4,
                'notice_id' => $this->comparisonUnique('提现通知', 4),
                'type_name' => '提现通知',
                'title'     => '小程序财务通知',
                'page_path' => $this->getInitPagePath($this->comparisonUnique('提现通知', 4)) ?? '',
            ],
            [
                'status'    => 0,
                'type'      => 4,
                'notice_id' => $this->comparisonUnique('提现失败通知', 4),
                'type_name' => '提现失败通知',
                'title'     => '小程序财务通知',
                'page_path' => $this->getInitPagePath($this->comparisonUnique('提现失败通知', 4)) ?? '',
            ],
            [
                'status'    => 0,
                'type'      => 4,
                'notice_id' => $this->comparisonUnique('分成收入通知', 4),
                'type_name' => '分成收入通知',
                'title'     => '小程序内容通知',
                'page_path' => $this->getInitPagePath($this->comparisonUnique('分成收入通知', 4)) ?? '',
            ],
            [
                'status'    => 0,
                'type'      => 4,
                'notice_id' => $this->comparisonUnique('问答提问通知', 4),
                'type_name' => '问答提问通知',
                'title'     => '小程序问答通知',
                'page_path' => $this->getInitPagePath($this->comparisonUnique('问答提问通知', 4)) ?? '',
            ],
            [
                'status'    => 0,
                'type'      => 4,
                'notice_id' => $this->comparisonUnique('问答回答通知', 4),
                'type_name' => '问答回答通知',
                'title'     => '小程序问答通知',
                'page_path' => $this->getInitPagePath($this->comparisonUnique('问答回答通知', 4)) ?? '',
            ],
            [
                'status'    => 0,
                'type'      => 4,
                'notice_id' => $this->comparisonUnique('问答过期通知', 4),
                'type_name' => '问答过期通知',
                'title'     => '小程序问答通知',
                'page_path' => $this->getInitPagePath($this->comparisonUnique('问答过期通知', 4)) ?? '',
            ],
            [
                'status'    => 0,
                'type'      => 4,
                'notice_id' => $this->comparisonUnique('得到红包通知', 4),
                'type_name' => '得到红包通知',
                'title'     => '小程序红包通知',
                'page_path' => $this->getInitPagePath($this->comparisonUnique('得到红包通知', 4)) ?? '',
            ],
            [
                'status'    => 0,
                'type'      => 4,
                'notice_id' => $this->comparisonUnique('悬赏问答通知', 4),
                'type_name' => '悬赏问答通知',
                'title'     => '小程序悬赏通知',
                'page_path' => $this->getInitPagePath($this->comparisonUnique('悬赏问答通知', 4)) ?? '',
            ],
            [
                'status'    => 0,
                'type'      => 4,
                'notice_id' => $this->comparisonUnique('悬赏过期通知', 4),
                'type_name' => '悬赏过期通知',
                'title'     => '小程序悬赏通知',
                'page_path' => $this->getInitPagePath($this->comparisonUnique('悬赏过期通知', 4)) ?? '',
            ],
        ];
    }

}
