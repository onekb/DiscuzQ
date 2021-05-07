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

namespace App\Api\Controller\Threads;

use App\Commands\Thread\EditThread;
use App\Common\ResponseCode;
use App\Models\Thread;
use Discuz\Auth\AssertPermissionTrait;
use Discuz\Base\DzqController;
use Illuminate\Contracts\Bus\Dispatcher;

class UpdateThreadV2Controller extends DzqController
{
    use AssertPermissionTrait;

    protected $bus;

    public $providers = [
    ];


    public function __construct(Dispatcher $bus)
    {
        $this->bus = $bus;
    }

    public function main()
    {
        //参数校验
        $thread_id= $this->inPut('id');
        if(empty($thread_id))     return  $this->outPut(ResponseCode::INVALID_PARAMETER);

        $threadRow = Thread::query()->where('id',$thread_id)->first();
        if(empty($threadRow)){
            return  $this->outPut(ResponseCode::INVALID_PARAMETER,"主题id".$thread_id."不存在");
        }

        $categoriesId = $this->inPut('categoriesId');
        $type = $this->inPut('type');

        //当传分类时有默认
        $isAnonymous = $this->inPut('isAnonymous');
        $price = $this->inPut('price');
        $freeWords = $this->inPut('freeWords');
        $attachment_price = $this->inPut('attachmentPrice');
        $location = $this->inPut('location');
        $latitude = $this->inPut('latitude');
        $longitude = $this->inPut('longitude');
        $isApproved = $this->inPut('isApproved');
        $isOldDraft = $this->inPut('isOldDraft');
        //没有默认
        $address = $this->inPut('address');
        $isDraft = $this->inPut('isDraft');
        $isEssence = $this->inPut('isEssence');
        $isDeleted = $this->inPut('isDeleted');
        $isSticky = $this->inPut('isSticky');
        $isFavorite = $this->inPut('isFavorite');
        $isSite = $this->inPut('isSite');
        $message = $this->inPut('message');
        $title = $this->inPut('title');
        $fileName = $this->inPut('fileName');
        $fileId = $this->inPut('fileId');

        $attributes = [];
        $requestData = [];
        if($categoriesId){
            $requestData = [
                "type" => "threads",
                "relationships" =>  [
                    "category" =>  [
                        "data" =>  [
                            "type" => "categories",
                            "id" => $categoriesId
                        ]
                    ],
                ]
            ];
            $attributes['type'] = (string)$type;
            $attributes['is_anonymous'] = $isAnonymous  ? $isAnonymous : false;
            $attributes['price'] = $price  ? $price: 0;
            $attributes['free_words'] = $freeWords  ? $freeWords : 0;
            $attributes['attachment_price'] = $attachment_price  ? $attachment_price: 0;
            $attributes['location'] = $location  ? $location : "";
            $attributes['latitude'] = $latitude  ? $latitude: "";
            $attributes['longitude'] = $longitude  ? $longitude : "";
            $attributes['is_anonymous'] = $isApproved  ? $isApproved : 0;
            $attributes['is_old_draft'] = $isOldDraft  ? $isOldDraft : 0;
        }

        if($isEssence || $isEssence===false){
            $attributes['isEssence'] = $isEssence;
        }
        if($isSticky || $isSticky===false){
            $attributes['isSticky'] = $isSticky;
        }
        if($isDeleted || $isDeleted===false){
            $attributes['isDeleted'] = $isDeleted;
        }
        if($isFavorite || $isFavorite===false){
            $attributes['isFavorite'] = $isFavorite;
        }
        if($isSite || $isSite===false){
            $attributes['isSite'] = $isSite;
        }

        if(!empty($address)){
            $attributes['address'] = $address;
        }
        if($isDraft || $isDraft===0){
            $attributes['is_draft'] = $isDraft;
        }

        if(!empty($message)){
            $attributes['message'] = $message;
        }
        if(!empty($title)){
            $attributes['title'] = $title;
        }
        if(!empty($fileName)){
            $attributes['fileName'] = $fileName;
        }
        if(!empty($fileId)){
            $attributes['fileId'] = $fileId;
        }

        $requestData['id'] = $thread_id;
        $requestData['type'] = 'threads';
        $requestData['attributes'] = $attributes;
        $result = $this->bus->dispatch(
            new EditThread($thread_id, $this->user, $requestData)
        );
        $result = $this->camelData($result);

        return $this->outPut(ResponseCode::SUCCESS,'', $result);

    }


}
