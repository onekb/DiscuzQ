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

use App\Api\Serializer\FirstStatisticsSerializer;
use App\Commands\Statistic\FirstStatistics;
use App\Common\ResponseCode;
use App\Models\Finance;
use App\Repositories\UserRepository;
use Carbon\Carbon;
use Discuz\Base\DzqController;
use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Support\Arr;


class FirstChartController extends DzqController
{
    public $serializer = FirstStatisticsSerializer::class;

    const CREATE_AT_BEGIN = '-60 days'; //默认统计周期

    /**
     * @var Dispatcher
     */
    protected $bus;

    /**
     * @param Dispatcher $bus
     */
    public function __construct(Dispatcher $bus)
    {
        $this->bus = $bus;
    }

    protected function checkRequestPermissions(UserRepository $userRepo)
    {
        return $this->user->isAdmin();
    }

    /**
     * {@inheritdoc}
     */
    public function main()
    {

        $actor = $this->user;
        $filter = $this->inPut('filter');
        $type   = $this->inPut('type',Finance::TYPE_DAYS);
        $createdAtBegin = Arr::get($filter, 'createdAtBegin', Carbon::parse(self::CREATE_AT_BEGIN)->toDateString());
        $createdAtEnd = Arr::get($filter, 'createdAtEnd', Carbon::now()->toDateString());

        $result = $this->bus->dispatch(
            new FirstStatistics($actor, $type, $createdAtBegin, $createdAtEnd)
        );

        return $this->outPut(ResponseCode::SUCCESS,'', $result);
    }
}
