<?php

namespace App\Api\Controller\OrderV3;

use App\Common\ResponseCode;
use App\Models\Order;
use App\Models\User;
use App\Models\Thread;
use App\Models\Post;
use App\Repositories\UserRepository;
use Discuz\Auth\Exception\PermissionDeniedException;
use Discuz\Base\DzqController;
use Illuminate\Support\Str;

class UsersOrderLogsListController extends DzqController
{
    protected function checkRequestPermissions(UserRepository $userRepo)
    {
        if (!$this->user->isAdmin()) {
            throw new PermissionDeniedException('没有权限');
        }
        return true;
    }

    public function main()
    {
        $currentPage = $this->inPut('page');
        $perPage = $this->inPut('perPage');
        $filter = (array)$this->inPut('filter');

        $query = Order::query();
        $query->select('orders.id as orderId', 'orders.user_id', 'orders.payee_id', 'orders.thread_id','users.nickname', 'orders.order_sn', 'orders.type', 'orders.amount', 'orders.status', 'orders.created_at');
        $query->join('users', 'orders.user_id', '=', 'users.id');
        if (isset($filter['orderSn']) && !empty($filter['orderSn'])) {
            $query->where('orders.order_sn', $filter['orderSn']);
        }

        if (isset($filter['status']) && is_numeric($filter['status'])) {
            $query->where('orders.status', $filter['status']);
        }

        if (isset($filter['startTime']) && !empty($filter['startTime'])) {
            $query->where('orders.created_at', '>=', $filter['startTime']);
        }

        if (isset($filter['endTime']) && !empty($filter['endTime'])) {
            $query->where('orders.created_at', '<=', $filter['endTime']);
        }

        // 发起方
        if (isset($filter['nickname']) && !empty($filter['nickname'])) {
            $query->where('users.nickname', 'like', '%' . $filter['nickname'] . '%');
        }

        // 收入方
        if (isset($filter['payeeNickname']) && !empty($filter['payeeNickname'])) {
            $payeeIds = User::query()->where('nickname', 'like', '%' . $filter['payeeNickname'] . '%')->pluck('id')->toArray();
            $query->whereIn('orders.payee_id', $payeeIds);
        }

        // 商品
        if (isset($filter['product']) && !empty($filter['product'])) {
            $product = $filter['product'];
            $query->when($product, function ($query, $product) {
                $query->whereIn(
                    'orders.thread_id',
                    Thread::query()
                        ->whereIn(
                            'id',
                            Post::query()->where('is_first', true)->where('content', 'like', "%$product%")->pluck('thread_id')
                        )
                        ->orWhere('threads.title', 'like', "%$product%")
                        ->pluck('id')
                );
            });
        }

        $query->orderByDesc('orders.created_at');
        $usersOrderLogs = $this->pagination($currentPage, $perPage, $query);

        $orders = $usersOrderLogs['pageData'];
        $orderThreadIds = array_column($orders, 'thread_id');
        $payeeUserIds = array_column($orders, 'payee_id');
        $payeeUserDatas = User::instance()->getUsers($payeeUserIds);
        $payeeUserDatas = array_column($payeeUserDatas, null, 'id');
        foreach ($orderThreadIds as $key => $value) {
            if (empty($value)) {
                unset($orderThreadIds[$key]);
            }
        }
        $orderThreadIds = array_merge($orderThreadIds);
        $threadData = $this->getThreadsBuilder($orderThreadIds);
        $threadData = array_column($threadData, null, 'threadId');
        foreach ($orders as $key => $value) {
            $orders[$key]['payeeNickname'] = $payeeUserDatas[$value['payee_id']]['nickname'] ?? '';
            $orders[$key]['thread'] = $threadData[$value['thread_id']] ?? ['title' => '该订单暂无对应帖子'];
            if(empty($orders[$key]['thread']['title'])){
                if(Str::length($orders['threads']['content']) > Thread::ORDER_TITLE_LENGTH ){
                    $orders[$key]['thread']['title'] = Str::finish(Str::substr(strip_tags($orders[$key]['content']), 0, Thread::ORDER_TITLE_LENGTH), Thread::ORDER_TITLE_END_WITH);
                }else{
                    $orders[$key]['thread']['title'] = strip_tags($orders[$key]['thread']['content']);
                }
            }
        }

        $usersOrderLogs['pageData'] = $this->camelData($orders) ?? [];
        return $this->outPut(ResponseCode::SUCCESS, '', $usersOrderLogs);
    }

    private function getThreadsBuilder($orderThreadIds)
    {
        return Thread::query()
            ->select('threads.id as threadId', 'threads.user_id', 'threads.title', 'posts.content')
            ->join('posts', 'threads.id', '=', 'posts.thread_id')
            ->where('posts.is_first', 1)
            ->whereIn('threads.id', $orderThreadIds)
            ->get()->toArray();
    }
}
