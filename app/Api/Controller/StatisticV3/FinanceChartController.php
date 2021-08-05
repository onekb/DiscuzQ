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

namespace App\Api\Controller\StatisticV3;

use App\Common\ResponseCode;
use App\Models\Finance;
use App\Repositories\FinanceRepository;
use App\Repositories\UserRepository;
use Carbon\Carbon;
use Discuz\Auth\Exception\PermissionDeniedException;
use Discuz\Base\DzqController;
use Illuminate\Contracts\Bus\Dispatcher;

class FinanceChartController extends DzqController
{
    const CREATE_AT_BEGIN = '-60 days'; //默认统计周期

    /**
     * @var Dispatcher
     */
    protected $bus;
    /**
     * @var FinanceRepository
     */
    private $finance;
    /**
     * @var int
     */
    private $type;
    /**
     * @var array|\ArrayAccess|mixed
     */
    private $createdAtBegin;
    /**
     * @var array|\ArrayAccess|mixed
     */
    private $createdAtEnd;

    /**
     * @param FinanceRepository $finance
     */
    public function __construct(FinanceRepository $finance)
    {
        $this->finance = $finance;
    }

    protected function checkRequestPermissions(UserRepository $userRepo)
    {
        if (!$this->user->isAdmin()) {
            throw new PermissionDeniedException('没有权限');
        }
        return true;
    }

    public function main()
    {
        $this->type             = !empty($this->inPut("type")) ? (int) $this->inPut("type") : Finance::TYPE_DAYS;
        $this->createdAtBegin   = !empty($this->inPut("createdAtBegin"))
                                    ? $this->inPut("createdAtBegin") : Carbon::parse(self::CREATE_AT_BEGIN)->toDateString();
        $this->createdAtEnd     = !empty($this->inPut("createdAtEnd"))
                                    ? $this->inPut("createdAtEnd") : Carbon::now()->toDateString();

        $financeChart = call_user_func([$this, '__invoke']);

        return $this->outPut(ResponseCode::SUCCESS,'',$this->camelData($financeChart));
    }

    /**
     * @return mixed
     * @throws PermissionDeniedException
     */
    public function __invoke()
    {
        $query = $this->finance->query();
        $query->whereBetween('created_at', [$this->createdAtBegin, $this->createdAtEnd]);

        if ($this->type !== Finance::TYPE_DAYS) {
            $format = '';
            if ($this->type == Finance::TYPE_WEEKS) {
                $format = '%Y/%u'.app('translator')->get('statistic.week');
            } elseif ($this->type == Finance::TYPE_MONTH) {
                $format = '%Y/%m'.app('translator')->get('statistic.month');
            }
            $query->selectRaw(
                "DATE_FORMAT(created_at,'{$format}') as `date`,".
                'SUM(order_count) as order_count,'.
                'SUM(order_amount) as order_amount,'.
                'SUM(total_profit) as total_profit,'.
                'SUM(register_profit) as register_profit,'.
                'SUM(master_portion) as master_portion,'.
                'SUM(withdrawal_profit) as withdrawal_profit'
            );
            $query->groupBy('date');
            $query->orderBy('date', 'asc');
        } else {
            $query->selectRaw("*, DATE_FORMAT(created_at,'%Y/%m/%d') as `date` ");
        }

        return $query->get();
    }
}
