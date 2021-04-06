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

namespace App\Api\Serializer;

use App\Models\Category;
use App\Models\Sequence;
use Discuz\Api\Serializer\AbstractSerializer;
use Tobscure\JsonApi\Relationship;

class CategorySerializer extends AbstractSerializer
{
    /**
     * {@inheritdoc}
     */
    protected $type = 'categories';

    /**
     * @param Category $model
     * @return array
     */
    public function getDefaultAttributes($model)
    {
        return [
            'id'                => $model->id,
            'name'              => $model->name,
            'description'       => $model->description,
            'icon'              => $model->icon,
            'sort'              => (int) $model->sort,
            'parentid'          => (int) $model->parentid,
            'property'          => (int) $model->property,
            'thread_count'      => (int) $model->thread_count,
            'ip'                => $model->ip,
            'created_at'        => $this->formatDate($model->created_at),
            'updated_at'        => $this->formatDate($model->updated_at),
            'canCreateThread'   => $this->actor->can('createThread', $model),
            'checked'           => in_array($model->id, $this->getCheckList()) ? 1 : 0,
            'search_ids'        => $this->getSearchIds($model->id),
            'children'          => $this->getChildrenList($model->id)
        ];
    }

    /**
     * @param Category $category
     * @return Relationship
     */
    protected function moderators($category)
    {
        return $this->hasMany($category, UserSerializer::class, 'moderatorUsers');
    }

    /**
     * @param  $list
     * @return array
     */
    protected function getChildrenList($parentid)
    {
        $list = Category::query()->where('parentid',$parentid)->orderBy('sort')->get();
        foreach ($list as $key => $value) {
            $list[$key]['created_at'] = $this->formatDate($value['created_at']);
            $list[$key]['canCreateThread'] = $this->actor->can('createThread', $value);
            $list[$key]['search_ids'] = $value['id'];
            $list[$key]['checked'] = in_array($value['id'], $this->getCheckList()) ? 1 : 0;
        }
        return $list;
    }

    /**
     * @param  $searchIds
     * @return array
     */
    protected function getSearchIds($id)
    {
        $searchIds = Category::query()->where('id', $id)->orWhere('parentid', $id)->orderBy('id')->pluck('id');
        $searchIds = json_decode($searchIds, true);
        $searchIds = implode(",",$searchIds);
        return $searchIds;
    }

    /**
     * $ids
     *
     * @param $ids
     * @return string
     */
    public function getCheckList(){
        $sequenceList = Sequence::query()->first();
        if (empty($sequenceList)) return [];
        $ids = explode(',',$sequenceList['category_ids']);
        return $ids;
    }
}
