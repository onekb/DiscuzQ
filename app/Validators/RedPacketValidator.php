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

namespace App\Validators;

use Discuz\Foundation\AbstractValidator;

class RedPacketValidator extends AbstractValidator
{
    /**
     * {@inheritdoc}
     */
    protected function getRules()
    {
        return [
            'rule'          => 'required|in:0,1',
            'condition'     => 'in:0,1',
            'likenum'       => 'int',
            'number'        => 'required|int|gt:0',
            'money'         => [
                                    'required',
                                    'regex:/^(([1-9][0-9]*)|(([0]\.\d{0,2}|[1-9][0-9]*\.\d{0,2})))$/'
                                ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function getMessages()
    {
        return [
            'rule.required'     => trans('redpacket.redpacket_rule_not_found'),
            'rule.in'           => trans('redpacket.redpacket_in_illegal'),
            'condition.in'      => trans('redpacket.redpacket_condition_illegal'),
            'likenum.int'       => trans('redpacket.redpacket_likenum_not_integer'),
            'likenum.gt'        => trans('redpacket.redpacket_likenum_gt_zero'),
            'number.required'   => trans('redpacket.redpacket_number_illegal'),
            'number.int'        => trans('redpacket.redpacket_number_illegal'),
            'number.gt'         => trans('redpacket.redpacket_number_illegal'),
            'money.required'    => trans('redpacket.redpacket_money_format_error'),
            'money.regex'       => trans('redpacket.redpacket_money_regex_error'),
        ];
    }
}
