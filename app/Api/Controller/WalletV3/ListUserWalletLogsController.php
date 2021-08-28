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

namespace App\Api\Controller\WalletV3;


use App\Api\Serializer\UserWalletLogSerializer;
use App\Common\ResponseCode;
use App\Models\User;
use App\Models\UserWalletLog;
use App\Repositories\UserRepository;
use App\Repositories\UserWalletLogsRepository;
use Discuz\Base\DzqController;
use Discuz\Http\UrlGenerator;
use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Tobscure\JsonApi\Parameters;
use App\Api\Controller\ThreadsV3\ThreadHelper;

class ListUserWalletLogsController extends DzqController
{
    protected $bus;
    protected $url;
    protected $cash;
    protected $walletLogs;
    protected $sumChangeAvailableAmount;
    protected $walletLogType;

    const TITLE_LENGTH = 5;

    public $include = [
        'user',
        'order',
        'order.user'
    ];

    public $optionalInclude = [
        'user',
        'userWallet',
        'sourceUser',
        'userWalletCash',
        'order.thread',
        'order.thread.user',
        'order.thread.firstPost',
    ];

    public $sort = [
        'created_at'    =>  'desc'
    ];

    public $sortFields = [
        'created_at',
        'updated_at'
    ];

    public $titleLength = 10;

    public function __construct(Dispatcher $bus, UrlGenerator $url, UserWalletLogsRepository $walletLogs)
    {
        $this->bus = $bus;
        $this->url = $url;
        $this->walletLogs = $walletLogs;
    }

    // 权限检查
    protected function checkRequestPermissions(UserRepository $userRepo)
    {
        $actor = $this->user;
        if ($actor->isGuest()) {
            $this->outPut(ResponseCode::JUMP_TO_LOGIN);
        }
        return true;
    }


    public function main()
    {
        $user_wallet_log_serializer = $this->app->make(UserWalletLogSerializer::class);
        $user_wallet_log_serializer->setRequest($this->request);

        $this->walletLogType  = $this->inPut('walletLogType');
        $filter         = $this->inPut('filter') ?: [];
        $page           = $this->inPut('page') ?: 1;
        $perPage        = $this->inPut('perPage') ?: 5;
        $requestInclude = explode(',', 'userWallet,order.thread.firstPost');

        if(!empty($this->inPut('include')) && is_array($requestInclude) && array_diff($requestInclude, $this->optionalInclude)){       //如果include 超出optionalinclude 就报错
            return $this->outPut(ResponseCode::NET_ERROR);
        }
        $sort           = (new Parameters($this->request->getQueryParams()))->getSort($this->sortFields) ?: $this->sort;
        $include        = !empty($requestInclude)
                            ? array_unique(array_merge($this->include, $requestInclude))
                            : $this->include;

        $walletLogs = $this->search($this->user, $filter, $sort, $page, $perPage);
        $walletLogType = $this->walletLogType;
        $walletLogs['pageData']->load('order.thread.firstPost')
            ->map(function (UserWalletLog $log) use($walletLogType) {
                /*
                if ($log->order && $log->order->thread) {
                    if ($log->order->thread->title) {
                        $title = Str::limit($log->order->thread->title);
                    } else {
                        $title = Str::limit($log->order->thread->firstPost->content);
                        $title = str_replace("\n", '', $title);
                    }
                $log->order->thread->title = strip_tags($title);
                }
                */
                switch ($log->change_type){
                    case UserWalletLog::TYPE_INCOME_REWARD:
                        $log->title = !empty($log->order->user->nickname) ? Str::limit($log->order->user->nickname, self::TITLE_LENGTH).'打赏了你的主题' : '';
                        break;
                    case UserWalletLog::TYPE_INCOME_SCALE_REWARD:
                        $log->title = !empty($log->order->user->nickname) ? Str::limit($log->order->user->nickname, self::TITLE_LENGTH).'打赏了帖子' : '';
                        break;
                    case UserWalletLog::TYPE_INCOME_ARTIFICIAL:
                        $log->title = '管理员充值';
                        break;
                    case in_array($log->change_type, [UserWalletLog::TYPE_INCOME_TEXT, UserWalletLog::TYPE_INCOME_LONG, UserWalletLog::TYPE_REDPACKET_INCOME]):
                        $log->title = '领取了红包';
                        break;
                    case in_array($log->change_type, [UserWalletLog::TYPE_TEXT_RETURN_THAW, UserWalletLog::TYPE_LONG_RETURN_THAW, UserWalletLog::TYPE_REDPACKET_REFUND]):
                        $log->title = '红包过期退还';
                        break;
                    case in_array($log->change_type, [UserWalletLog::TYPE_INCOME_THREAD_REWARD, UserWalletLog::TYPE_QUESTION_REWARD_INCOME, UserWalletLog::TYPE_INCOME_QUESTION_REWARD]):
                        $log->title = '获取了赏金';
                        break;
                    case $walletLogType == 'income' && in_array($log->change_type, [UserWalletLog::TYPE_INCOME_THREAD_REWARD_RETURN, UserWalletLog::TYPE_QUESTION_REWARD_REFUND]):
                        $log->title = '赏金被退回';
                        break;
                    case in_array($log->change_type, [UserWalletLog::TYPE_INCOME_ONLOOKER_REWARD, UserWalletLog::TYPE_INCOME_THREAD, UserWalletLog::TYPE_INCOME_SCALE_THREAD, UserWalletLog::TYPE_INCOME_ATTACHMENT, UserWalletLog::TYPE_INCOME_SCALE_ATTACHMENT]):
                        $log->title = !empty($log->order->user->nickname) ? Str::limit($log->order->user->nickname, self::TITLE_LENGTH).'支付了帖子' : '';
                        break;
                    case UserWalletLog::TYPE_INCOME_SCALE_REGISTER:
                        $log->title = !empty($log->order->user->nickname) ? Str::limit($log->order->user->nickname, self::TITLE_LENGTH).'注册了站点' : '';
                        break;
                    case in_array($log->change_type, [UserWalletLog::TYPE_TEXT_ABNORMAL_REFUND, UserWalletLog::TYPE_LONG_ABNORMAL_REFUND, UserWalletLog::TYPE_QUESTION_ABNORMAL_REFUND, UserWalletLog::TYPE_ABNORMAL_ORDER_REFUND]):
                        $log->title = '异常订单退款';
                        break;
                    case UserWalletLog::TYPE_QUESTION_ORDER_ABNORMAL_REFUND:
                        $log->title = '悬赏异常退回';
                        break;
                    case UserWalletLog::TYPE_REDPACKET_ORDER_ABNORMAL_REFUND:
                        $log->title = '红包异常退回';
                        break;
                    case UserWalletLog::TYPE_CASH_THAW:
                        $log->title = '提现金额已返还';
                        break;
                    case UserWalletLog::TYPE_EXPEND_REGISTER:
                        $log->title = '注册站点';
                        break;
                    case UserWalletLog::TYPE_EXPEND_ARTIFICIAL:
                        $log->title = '管理员扣除';
                        break;
                    case UserWalletLog::TYPE_QUESTION_REWARD_FREEZE:
                        $log->title = '发出了悬赏';
                        break;
                    case in_array($log->change_type, [UserWalletLog::TYPE_EXPEND_THREAD, UserWalletLog::TYPE_EXPEND_ONLOOKER, UserWalletLog::TYPE_EXPEND_ATTACHMENT]):
                        $log->title = '付费查看了帖子';
                        break;
                    case $walletLogType == 'expend' && in_array($log->change_type, [UserWalletLog::TYPE_TEXT_FREEZE, UserWalletLog::TYPE_LONG_FREEZE, UserWalletLog::TYPE_REDPACKET_FREEZE]):
                        $log->title = '发出了红包';
                        break;
                    case UserWalletLog::TYPE_EXPEND_RENEW:
                        $log->title = '站点续费';
                        break;
                    case $walletLogType == 'expend' && $log->change_type == UserWalletLog::TYPE_CASH_FREEZE:
                        $log->title = '提现';
                        break;
                    case UserWalletLog::TYPE_EXPEND_REWARD:
                        $log->title = '打赏了帖子';
                        break;
                    case in_array($log->change_type, [UserWalletLog::TYPE_QUESTION_FREEZE, UserWalletLog::TYPE_QUESTION_REWARD_FREEZE]):
                        $log->title = '悬赏冻结';
                        break;
                    case $walletLogType == 'freeze' && in_array($log->change_type, [UserWalletLog::TYPE_QUESTION_REWARD_FREEZE_RETURN, UserWalletLog::TYPE_QUESTION_RETURN_THAW, UserWalletLog::TYPE_QUESTION_REWARD_EXPEND]):
                        $log->title = '悬赏解冻';
                        break;
                    case $walletLogType == 'freeze' && in_array($log->change_type, [UserWalletLog::TYPE_TEXT_FREEZE, UserWalletLog::TYPE_LONG_FREEZE, UserWalletLog::TYPE_REDPACKET_FREEZE]):
                        $log->title = '红包冻结';
                        break;
                    case $walletLogType == 'freeze' && in_array($log->change_type, [UserWalletLog::TYPE_REDPACKET_REFUND, UserWalletLog::TYPE_LONG_RETURN_THAW, UserWalletLog::TYPE_TEXT_RETURN_THAW, UserWalletLog::TYPE_EXPEND_LONG, UserWalletLog::TYPE_EXPEND_TEXT, UserWalletLog::TYPE_REDPACKET_EXPEND]):
                        $log->title = '红包解冻';
                        break;
                    case $walletLogType == 'freeze' && $log->change_type == UserWalletLog::TYPE_CASH_FREEZE:
                        $log->title = '提现';
                        break;
                    case $walletLogType == 'freeze' && in_array($log->change_type, [UserWalletLog::TYPE_CASH_SUCCESS, UserWalletLog::TYPE_CASH_THAW]):
                        $log->title = '提现解冻';
                        break;
                    case $walletLogType == 'freeze' && $log->change_type == UserWalletLog::TYPE_MERGE_FREEZE:
                        $log->title = '合并订单冻结';
                        break;
                    case $walletLogType == 'expend' && $log->change_type == UserWalletLog::TYPE_MERGE_FREEZE:
                        $log->title = '合并订单支出';
                        break;
                    case $walletLogType == 'freeze' && $log->change_type == UserWalletLog::TYPE_MERGE_REFUND:
                        $log->title = '合并订单退回';
                        break;
                    case $walletLogType == 'income' && $log->change_type == UserWalletLog::TYPE_MERGE_REFUND:
                        $log->title = '合并订单收入';
                        break;
                    default:
                        break;
                }

            });

        $data = $this->camelData($walletLogs);

        $data = $this->filterData($data);

        return $this->outPut(ResponseCode::SUCCESS,'', $data);
    }


    public function search($actor, $filter, $sort, $page = 0, $perPage = 0)
    {
        $query = $this->walletLogs->query();
        $this->applyFilters($query, $filter, $actor);
//        print_r([$query->toSql(), $query->getBindings()]);die;
        // 求和变动可用金额
        $this->sumChangeAvailableAmount = number_format($query->sum('change_available_amount'), 2);

        foreach ((array)$sort as $field => $order) {
            $query->orderBy(Str::snake($field), $order);
        }
        return $this->pagination($page, $perPage, $query, false);
    }


    private function applyFilters(Builder $query, array $filter, User $actor)
    {
        $log_user           = (int)Arr::get($filter, 'user'); //用户
        $log_change_desc    = Arr::get($filter, 'changeDesc'); //变动描述
        $log_change_type    = Arr::get($filter, 'changeType', []); //变动类型
        $log_username       = Arr::get($filter, 'username'); //变动钱包所属人
        $log_start_time     = Arr::get($filter, 'startTime'); //变动时间范围：开始
        $log_end_time       = Arr::get($filter, 'endTime'); //变动时间范围：结束
        $log_source_user_id         = Arr::get($filter, 'sourceUserId');
        $log_change_type_exclude    = Arr::get($filter, 'changeTypeExclude');//排除变动类型
        //收入类型：
        $income_type = [
            UserWalletLog::TYPE_INCOME_REWARD,          //打赏收入，31
            UserWalletLog::TYPE_INCOME_SCALE_REWARD,          //分成打赏收入，33
            UserWalletLog::TYPE_INCOME_ARTIFICIAL,          //人工收入，32
            //红包收入 start
            UserWalletLog::TYPE_INCOME_TEXT,          //文字帖红包收入，102
            UserWalletLog::TYPE_INCOME_LONG,          //长文帖红包收入，112
            UserWalletLog::TYPE_REDPACKET_INCOME,          //红包收入，151
            //红包收入 end
            //红包退回 start
            UserWalletLog::TYPE_REDPACKET_REFUND,          //红包退款，152
            UserWalletLog::TYPE_TEXT_RETURN_THAW,          //文字帖红包冻结返还，103
            UserWalletLog::TYPE_LONG_RETURN_THAW,          //长文帖红包冻结返还，113
            //红包退回 end
            //悬赏问答收入 start
            UserWalletLog::TYPE_INCOME_THREAD_REWARD,          //悬赏问答收入，120
            UserWalletLog::TYPE_QUESTION_REWARD_INCOME,          //悬赏问答收入，161
            UserWalletLog::TYPE_INCOME_QUESTION_REWARD,          //问答答题收入，35
            //悬赏问答收入 end
            //悬赏退回 start
            UserWalletLog::TYPE_INCOME_THREAD_REWARD_RETURN,          //悬赏帖过期-悬赏帖剩余悬赏金额返回，121
            UserWalletLog::TYPE_QUESTION_REWARD_REFUND,          //悬赏问答退款，162
//            UserWalletLog::TYPE_QUESTION_REWARD_FREEZE_RETURN,          //悬赏冻结返回，165  -- 只扣除了 冻结金额，但是没有增加可用余额
            //悬赏退回 end
            //付费收入 start
            UserWalletLog::TYPE_INCOME_ONLOOKER_REWARD,          //问答围观收入，36
            UserWalletLog::TYPE_INCOME_THREAD,          //付费主题收入，60
            UserWalletLog::TYPE_INCOME_SCALE_THREAD,          //分成付费主题收入，62
            UserWalletLog::TYPE_INCOME_ATTACHMENT,          //付费附件收入，63
            UserWalletLog::TYPE_INCOME_SCALE_ATTACHMENT,          //付费附件分成收入，64
            //付费收入 end
            //注册分成收入 start
            UserWalletLog::TYPE_INCOME_SCALE_REGISTER,          //注册分成收入，34
            //注册分成收入 end
            //异常退款 start
            UserWalletLog::TYPE_TEXT_ABNORMAL_REFUND,          //文字帖红包订单异常返现，104
            UserWalletLog::TYPE_LONG_ABNORMAL_REFUND,          //长文帖红包订单异常返现，114
            UserWalletLog::TYPE_QUESTION_ABNORMAL_REFUND,          //问答帖订单异常返现，124
            UserWalletLog::TYPE_ABNORMAL_ORDER_REFUND,          //异常订单退款，130
            UserWalletLog::TYPE_QUESTION_ORDER_ABNORMAL_REFUND,          //悬赏订单异常退款，163
            UserWalletLog::TYPE_REDPACKET_ORDER_ABNORMAL_REFUND,          //红包订单异常退款，154
            //异常退款 end
            //提现失败 start
            UserWalletLog::TYPE_CASH_THAW,          //提现解冻，提现失败，12
        ];

        // 所有支出类型：
            $expend_type = [
                //注册支出 start
                UserWalletLog::TYPE_EXPEND_REGISTER,            // 站点注册支出，70
                //注册支出 end
                //人工支出 start
                UserWalletLog::TYPE_EXPEND_ARTIFICIAL,          // 人工支出，50
                //人工支出 end
                //悬赏支出 start
                UserWalletLog::TYPE_QUESTION_REWARD_FREEZE,          // 悬赏问答冻结，160
                //悬赏支出 end
                //付费支出 start
                UserWalletLog::TYPE_EXPEND_THREAD,          // 付费主题支出，61
                UserWalletLog::TYPE_EXPEND_ONLOOKER,          // 问答围观支出，82
                UserWalletLog::TYPE_EXPEND_ATTACHMENT,          // 付费附件支出，52
                //付费支出 end
                //红包支出 start            红包支出要取  change_freeze_amount  字段
                UserWalletLog::TYPE_TEXT_FREEZE,          // 文字帖红包冻结，101
                UserWalletLog::TYPE_LONG_FREEZE,          // 长文帖红包冻结，111
                UserWalletLog::TYPE_REDPACKET_FREEZE,          // 红包冻结，150
                //红包支出 end
                //站点续费支出 start
                UserWalletLog::TYPE_EXPEND_RENEW,       //		站点续费支出，71
                //站点续费支出 end
                //提现支出 start
                UserWalletLog::TYPE_CASH_FREEZE,           //   提现冻结，10
                //提现支出 end
                //打赏支出 start
                UserWalletLog::TYPE_EXPEND_REWARD,           //	打赏支出，41
                //打赏支出 end
                //合并订单支出 start （这里还需要区分出 红包 + 悬赏）
                UserWalletLog::TYPE_MERGE_FREEZE,           //	合并订单冻结，170
                //合并订单支出 end
            ];
        // 所有冻结类型：
        $freeze_type = [
            //悬赏冻结 start
            UserWalletLog::TYPE_QUESTION_FREEZE,       //  问答冻结，8
            UserWalletLog::TYPE_QUESTION_REWARD_FREEZE, // 悬赏问答冻结，160
            //悬赏冻结 end
            //悬赏解冻 start
            UserWalletLog::TYPE_QUESTION_REWARD_FREEZE_RETURN,       //		悬赏冻结返回，165
//            UserWalletLog::TYPE_INCOME_THREAD_REWARD_RETURN,       //		悬赏帖过期-悬赏帖剩余悬赏金额返回，121， 这里与 上面的 165 冲突，只需要取上面的 165 即可
            UserWalletLog::TYPE_QUESTION_RETURN_THAW,       //		问答返还解冻，9
            //悬赏解冻 end
            //悬赏支出 start
            UserWalletLog::TYPE_QUESTION_REWARD_EXPEND,     //悬赏采纳支出，164
            //悬赏支出 end

            //红包冻结 start
            UserWalletLog::TYPE_TEXT_FREEZE,       //		文字帖红包冻结，101
            UserWalletLog::TYPE_LONG_FREEZE,       //		长文帖红包冻结，111
            UserWalletLog::TYPE_REDPACKET_FREEZE,       //		红包冻结，150
            //红包冻结 end
            //红包支出 start
            UserWalletLog::TYPE_EXPEND_TEXT,            // 文字帖红包支出, 100
            UserWalletLog::TYPE_EXPEND_LONG,            // 长文帖红包支出, 110
            UserWalletLog::TYPE_REDPACKET_EXPEND,            // 红包支出, 153
            //红包支出 end


            //红包解冻 start
            UserWalletLog::TYPE_REDPACKET_REFUND,       //		红包退款，152
            UserWalletLog::TYPE_LONG_RETURN_THAW,       //		长文帖冻结返还，113
            UserWalletLog::TYPE_TEXT_RETURN_THAW,       //		文字帖冻结返还，103
            //红包解冻 end
            //提现 start
            UserWalletLog::TYPE_CASH_FREEZE,       //		提现冻结，10
            UserWalletLog::TYPE_CASH_THAW,       //		提现解冻，提现失败，12
            UserWalletLog::TYPE_CASH_SUCCESS,       //		提现成功，11
            //提现 end

            //合并订单（冻结） start  红包 + 悬赏
            UserWalletLog::TYPE_MERGE_FREEZE,       //		合并订单冻结，170
            //合并订单（冻结） end
            //合并订单（解冻） start  红包 + 悬赏
            UserWalletLog::TYPE_MERGE_REFUND,       //		合并订单退款，171
            //合并订单（解冻） end
        ];


//        $query->when($log_user, function ($query) use ($log_user) {
//            $query->where('user_id', $log_user);
//        });
        $query->when($actor, function ($query) use ($actor) {
            $query->where('user_id', $actor->id);
        });

        $query->when($log_change_desc, function ($query) use ($log_change_desc) {
            $query->where('change_desc', 'like', "%$log_change_desc%");
        });

        if ($this->walletLogType == 'income') {
            $query->where('change_available_amount', '>', 0)->whereIn('change_type', $income_type);
        } elseif ($this->walletLogType == 'expend') {
            $query->whereIn('change_type', $expend_type);
        } elseif ($this->walletLogType == 'freeze') {
            $query->whereIn('change_type', $freeze_type);
        }
        if (!empty($log_change_type)) {
            $query->when($log_change_type, function ($query) use ($log_change_type) {
                $query->whereIn('change_type', $log_change_type);
            });
        }

        $query->when(!is_null($log_change_type_exclude), function ($query) use ($log_change_type_exclude) {
            $log_change_type_exclude = explode(',', $log_change_type_exclude);
            $query->whereNotIn('change_type', $log_change_type_exclude);
        });
        $query->when($log_start_time, function ($query) use ($log_start_time) {
            $query->where('created_at', '>=', $log_start_time);
        });
        $query->when($log_end_time, function ($query) use ($log_end_time) {
            $query->where('created_at', '<=', $log_end_time.' 23:59:59');
        });
        $query->when($log_username, function ($query) use ($log_username) {
            $query->whereIn('user_wallet_logs.user_id',
                            User::where('users.username', $log_username)
                                ->select('id', 'username')
                                ->get()
                            );
        });
        if (Arr::has($filter, 'source_username')) { // 有搜索 "0" 的情况
            $log_source_username = Arr::get($filter, 'source_username');
            $query->whereIn('user_wallet_logs.source_user_id',
                            User::query()
                                ->where('users.username', 'like', '%' . $log_source_username . '%')
                                ->pluck('id')
                            );
        }
        $query->when($log_source_user_id, function ($query) use ($log_source_user_id) {
            $query->where('source_user_id', '=', $log_source_user_id);
        });
    }

    public function filterData($data){
        foreach ($data['pageData'] as $key => $val) {
            /*
            if(!empty($val['order']['thread']['title'])) {
                $title = str_replace(['<r>', '</r>', '<t>', '</t>'], ['', '', '', ''], $val['order']['thread']['title']);
                list($searches, $replaces) = ThreadHelper::getThreadSearchReplace($title);
                $title = str_replace($searches, $replaces, $title);
            }elseif (!empty($val['order']['thread']['firstPost']['content'])) {
                $title = str_replace(['<r>', '</r>', '<t>', '</t>'], ['', '', '', ''], $val['order']['thread']['firstPost']['content']);
                list($searches, $replaces) = ThreadHelper::getThreadSearchReplace($title);
                $title = str_replace($searches, $replaces, $title);
            } else {
                $title = '';
            }
            */


            $amount = 0;
            switch ($this->walletLogType){
                case 'income':
                    $amount = $val['changeAvailableAmount'];
                    if($val['changeType'] == UserWalletLog::TYPE_MERGE_REFUND){
                        $val['changeDesc'] = '合并订单收入';
                    }
                    break;
                case 'expend':
                    //红包支出
                    if(in_array($val['changeType'], [UserWalletLog::TYPE_EXPEND_TEXT, UserWalletLog::TYPE_EXPEND_LONG, UserWalletLog::TYPE_REDPACKET_EXPEND])){
                        $amount = $val['changeFreezeAmount'];
                    }else{
                        $amount = $val['changeAvailableAmount'];
                    }
                    if($val['changeType'] == UserWalletLog::TYPE_MERGE_FREEZE){
                        $val['changeDesc'] = '合并订单支出';
                    }
                    break;
                case 'freeze':
                    $amount = $val['changeFreezeAmount'];
                    switch ($val['changeType']){
                        case in_array($val['changeType'], [UserWalletLog::TYPE_EXPEND_TEXT, UserWalletLog::TYPE_EXPEND_LONG, UserWalletLog::TYPE_REDPACKET_EXPEND,
                            UserWalletLog::TYPE_TEXT_RETURN_THAW, UserWalletLog::TYPE_LONG_RETURN_THAW, UserWalletLog::TYPE_TEXT_ABNORMAL_REFUND, UserWalletLog::TYPE_LONG_ABNORMAL_REFUND,
                            UserWalletLog::TYPE_REDPACKET_REFUND, UserWalletLog::TYPE_REDPACKET_ORDER_ABNORMAL_REFUND]):
                            $val['changeDesc'] = '红包解冻';
                            break;
                        case in_array($val['changeType'], [UserWalletLog::TYPE_QUESTION_REWARD_EXPEND, UserWalletLog::TYPE_QUESTION_REWARD_FREEZE_RETURN, UserWalletLog::TYPE_QUESTION_ORDER_ABNORMAL_REFUND,
                            UserWalletLog::TYPE_QUESTION_REWARD_REFUND, UserWalletLog::TYPE_QUESTION_ABNORMAL_REFUND, UserWalletLog::TYPE_INCOME_THREAD_REWARD_RETURN]):
                            $val['changeDesc'] = '悬赏解冻';
                            break;
                        case in_array($val['changeType'], [UserWalletLog::TYPE_MERGE_REFUND]):
                            $val['changeDesc'] = '合并订单退回';
                            break;
                        default:
                            break;
                    }
                    break;
            }

            $pageData = [
                'id'            =>  $val['id'],
                'title'         =>  $val['title'],
                'amount'        =>  $amount,
                'changeType'    =>  !empty($val['changeType']) ? $val['changeType'] : '',
                'changeDesc'    =>  !empty($val['changeDesc']) ? $val['changeDesc'] : '',
                'status'        =>  !empty($val['order']['status']) ? $val['order']['status'] : '',
                'createdAt'     =>  !empty($val['createdAt']) ? $val['createdAt'] : 0,
            ];
            $data['pageData'][$key] =  $pageData;
        }

        return $data;
    }


}
