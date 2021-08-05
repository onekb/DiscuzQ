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

namespace App\Api\Controller\SettingsV3;

use App\Common\ResponseCode;
use App\Models\Sequence;
use App\Models\Topic;
use App\Models\User;
use App\Repositories\UserRepository;
use Discuz\Base\DzqController;

class ListSequenceController extends DzqController
{
    protected function checkRequestPermissions(UserRepository $userRepo)
    {
        return $this->user->isAdmin();
    }

    public function main()
    {
        $sequence = Sequence::query()->first();
        $data = [];
        if($sequence){
            $sequence = $sequence->toArray();
            $userInfo = null;
            if(!empty($sequence['user_ids'])){
                $userIdArr = explode(',',$sequence['user_ids']);
                $userInfo = User::query()->whereIn('id',$userIdArr)->get(['id','username'])->toArray();
            }
            $blockUserInfo = null;
            if(!empty($sequence['block_user_ids'])){
                $blockUserIdArr = explode(',',$sequence['block_user_ids']);
                $blockUserInfo = User::query()->whereIn('id',$blockUserIdArr)->get(['id','username'])->toArray();
            }
            $topicInfo = null;
            if(!empty($sequence['topic_ids'])){
                $topicIdArr = explode(',',$sequence['topic_ids']);
                $topicInfo = Topic::query()->whereIn('id',$topicIdArr)->get(['id','content'])->toArray();
            }
            $blockTopicInfo = null;
            if(!empty($sequence['block_topic_ids'])){
                $blockTopicIdArr = explode(',',$sequence['block_topic_ids']);
                $blockTopicInfo = Topic::query()->whereIn('id',$blockTopicIdArr)->get(['id','content'])->toArray();
            }
            $sequence['userInfo'] = $userInfo;
            $sequence['blockUserInfo'] = $blockUserInfo;
            $sequence['topicInfo'] = $topicInfo;
            $sequence['blockTopicInfo'] = $blockTopicInfo;
            unset($sequence['user_ids']);
            unset($sequence['block_user_ids']);
            unset($sequence['topic_ids']);
            unset($sequence['block_topic_ids']);
            $data = $this->camelData($sequence);
        }
        return $this->outPut(ResponseCode::SUCCESS,'', $data);
    }
}
