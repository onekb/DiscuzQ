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

namespace App\Modules\ThreadTom\Busi;

use App\Common\CacheKey;
use App\Models\ThreadVote;
use App\Models\ThreadVoteSubitem;
use App\Models\ThreadVoteUser;
use Carbon\Carbon;
use Discuz\Base\DzqCache;
use App\Common\ResponseCode;
use App\Modules\ThreadTom\TomBaseBusi;
use TencentCloud\Iotcloud\V20180614\Models\ProductResourceInfo;

class VoteBusi extends TomBaseBusi
{
    const SUBITEMS_LENGTH = 50;

    public function create()
    {
        $input = $this->verification();
        $this->db->beginTransaction();
        // 先创建 thread_vote
        $thread_vote = ThreadVote::query()->create([
            'thread_id'    =>  $this->threadId,
            'expired_at'    =>  $input['expired_at'],
            'vote_title'    =>  $input['vote_title'],
            'choice_type'    =>  $input['choice_type']
        ]);
        if($thread_vote === false){
            $this->db->rollBack();
            $this->outPut(ResponseCode::INVALID_PARAMETER, '新增投票帖失败');
        }
        // 再创建 thread_vote_subitems
        $thread_vote_insert = [];
        foreach ($input['subitems'] as $val){
            $thread_vote_insert[] = [
                'thread_vote_id'    =>  $thread_vote->id,
                'content'           =>  $val['content'],
                'created_at'        =>  $thread_vote->created_at
            ];
        }
        $res = $this->db->table('thread_vote_subitems')->insert($thread_vote_insert);
        if($res === false){
            $this->db->rollBack();
            $this->outPut(ResponseCode::INVALID_PARAMETER, '新增投票帖失败');
        }
        $this->db->commit();
        //这里的格式暂时作为数组的形式存，方便以后一个帖子多个投票的时候扩展
        return $this->jsonReturn(['voteIds' => [$thread_vote->id]]);
    }

    public function update()
    {
        $input = $this->updateCheckVar();
        $this->db->beginTransaction();
        $thread_vote = ThreadVote::query()->find($input['vote_id']);
        if(empty($thread_vote)){
            $this->outPut(ResponseCode::INVALID_PARAMETER, '投票信息不存在');
        }
        //先修改 thread_vote
        $thread_vote->vote_title = $input['vote_title'];
        if($thread_vote->choice_type != $input['choice_type'] && $thread_vote->vote_users > 0){
            $this->db->rollBack();
            $this->outPut(ResponseCode::INVALID_PARAMETER, '已有人投票，不可更改选择类型');
        };
        $thread_vote->expired_at = $input['expired_at'];
        $res = $thread_vote->save();
        if($res === false){
            $this->db->rollBack();
            $this->outPut(ResponseCode::INTERNAL_ERROR, '修改投票信息出错');
        }
        //在修改 thread_vote_subitems
        $thread_vote_subitmes_ids = array_column($input['subitems'], 'id');
        //找出之前的 thread_vote_subitems 数据
        $thread_vote_subitems_old_ids = ThreadVoteSubitem::query()->where('thread_vote_id', $thread_vote->id)->whereNull('deleted_at')->pluck('id')->toArray();
        $remove_sub_ids = array_diff($thread_vote_subitems_old_ids, $thread_vote_subitmes_ids);
        //删除这次没有传对应id过来的
        $res = ThreadVoteSubitem::query()->whereIn('id', $remove_sub_ids)->whereNull('deleted_at')->update(['deleted_at' => $thread_vote->updated_at]);
        if($res === false){
            $this->db->rollBack();
            $this->outPut(ResponseCode::INTERNAL_ERROR, '修改投票选项出错');
        }
        //判断是否thread_vote_user 中对应 user_id 被删除完，更新 thread_votes 中 vote_users 字段数据
        $res = ThreadVoteUser::query()->whereIn('thread_vote_subitem_id', $remove_sub_ids)->delete();
        if($res === false){
            $this->db->rollBack();
            $this->outPut(ResponseCode::INTERNAL_ERROR, '删除相关投票出错');
        }
        $vote_users = ThreadVoteUser::query()->where('thread_id', $this->threadId)->groupBy('user_id')->count();
        $res = ThreadVote::query()->where('id', $thread_vote->id)->update(['vote_users' => $vote_users]);
        if($res === false){
            $this->db->rollBack();
            $this->outPut(ResponseCode::INTERNAL_ERROR, '更新投票人数出错');
        }
        //修改
        $insert_sub = [];
        foreach ($input['subitems'] as $val){
            if(!empty($val['id'])){
                $res = ThreadVoteSubitem::query()->where('id', $val['id'])->update(['content' => $val['content']]);
                if($res === false){
                    $this->db->rollBack();
                    $this->outPut(ResponseCode::INTERNAL_ERROR, '修改投票选项出错');
                }
            }else{
                $insert_sub[] = [
                    'thread_vote_id'    =>  $thread_vote->id,
                    'content'           =>  $val['content'],
                    'created_at'        =>  $thread_vote->updated_at
                ];
            }
        }
        if(!empty($insert_sub)){
            $res = $this->db->table('thread_vote_subitems')->insert($insert_sub);
            if($res === false){
                $this->db->rollBack();
                $this->outPut(ResponseCode::INVALID_PARAMETER, '编辑新增投票帖失败');
            }
        }
        $this->db->commit();
        //删除之前的缓存
        DzqCache::delHashKey(CacheKey::LIST_THREADS_V3_VOTES, $input['vote_id']);
        DzqCache::delHashKey(CacheKey::LIST_THREADS_V3_VOTE_SUBITEMS, $input['vote_id']);

        return $this->jsonReturn(['voteIds' => [$thread_vote->id]]);
    }

    public function select()
    {
        $voteIds = $this->getParams('voteIds');
        $votes = DzqCache::hMGetCollection(CacheKey::LIST_THREADS_V3_VOTES, $voteIds, function ($voteIds) {
            return ThreadVote::query()->whereIn('id', $voteIds)->whereNull('deleted_at')->get();
        });
        $res = [];
        if(!empty($votes->toArray())){
            $res = array_map(function ($item){
                $subitems = DzqCache::hGet(CacheKey::LIST_THREADS_V3_VOTE_SUBITEMS, $item['id'], function ($thread_vote_id) {
                    return ThreadVoteSubitem::query()->where('thread_vote_id', $thread_vote_id)->whereNull('deleted_at')->get();
                });
                $res_subitems = $subitems->toArray();
                //判断该用户是否投票
                $isVoted = false;
                $vote_ids = [];
                if(!$this->user->isGuest()){
                    $thread_vote_users_ids = ThreadVoteUser::query()->where(['thread_id' => $this->threadId, 'user_id' => $this->user->id])->pluck('thread_vote_subitem_id')->toArray();
                    $res_subitems_ids = array_column($res_subitems, 'id');
                    $vote_ids = array_intersect($thread_vote_users_ids, $res_subitems_ids);
                    if(!empty($vote_ids)){
                        $isVoted = true;
                    }
                }
                //计算投票选项的总票数
                $res_subitems_sum_votes = array_sum(array_column($res_subitems, 'vote_count'));
                foreach ($res_subitems as &$val){
                    $val['isVoted'] = in_array($val['id'], $vote_ids);
                    $val['voteRate'] = 0;
                    if(!empty($res_subitems_sum_votes))     $val['voteRate'] = round(($val['vote_count']/$res_subitems_sum_votes) * 100, 2).'%';
                    unset($val['thread_vote_id']);
                    unset($val['created_at']);
                    unset($val['updated_at']);
                    unset($val['deleted_at']);
                }
                return  [
                    'voteId'    =>  $item['id'],
                    'voteTitle' =>  $item['vote_title'],
                    'choiceType' => $item['choice_type'],
                    'voteUsers'  => $item['vote_users'],
                    'expiredAt'  => $item['expired_at'],
                    'isExpired'  => $item['expired_at'] < Carbon::now(),
                    'isVoted'   =>  $isVoted,
                    'subitems'  =>  $this->camelData($res_subitems)
                ];
            }, $votes->toArray());
        }
        return $this->jsonReturn($res);
    }

    public function verification(){
        $input = [
            'vote_title' => $this->getParams('voteTitle'),
            'choice_type' => $this->getParams('choiceType'),
            'expired_at'  => $this->getParams('expiredAt'),
            'subitems' => $this->getParams('subitems'),
        ];
        $rules = [
            'vote_title' => 'required|string|max:25',
            'choice_type' => 'required|int|in:1,2',
            'expired_at'  => 'required|date',
            'subitems' => 'required|array|min:2|max:20',
        ];
        $this->dzqValidate($input, $rules);
        if($input['expired_at'] < Carbon::now()){
            $this->outPut(ResponseCode::INVALID_PARAMETER,'过期时间不正确');
        }
        $all_content = [];
        foreach ($input['subitems'] as &$val){
            $val['content'] = trim($val['content']);
            if(empty($val['content']))      $this->outPut(ResponseCode::INVALID_PARAMETER, '投票选项内容不得为空');
            if(in_array($val['content'], $all_content))         $this->outPut(ResponseCode::INVALID_PARAMETER, '投票选项内容不得重复');
            $all_content[] = $val['content'];
            if(mb_strlen($val['content']) > self::SUBITEMS_LENGTH)    $this->outPut(ResponseCode::INVALID_PARAMETER, '投票选项最多50个字');
        }
        return $input;
    }

    public function updateCheckVar(){
        $input = [
            'vote_id' => $this->getParams('voteId'),
            'vote_title' => $this->getParams('voteTitle'),
            'choice_type' => $this->getParams('choiceType'),
            'expired_at'  => $this->getParams('expiredAt'),
            'subitems' => $this->getParams('subitems'),
        ];
        $rules = [
            'vote_id' => 'required|integer|min:1',
            'vote_title' => 'required|string|max:25',
            'choice_type' => 'required|int|in:1,2',
            'expired_at'  => 'required|date',
            'subitems' => 'required|array|min:2|max:20',
        ];
        $this->dzqValidate($input, $rules);
        if($input['expired_at'] < Carbon::now()){
            $this->outPut(ResponseCode::INVALID_PARAMETER,'过期时间不正确');
        }
        $all_content = [];
        foreach ($input['subitems'] as &$val){
            $val['content'] = trim($val['content']);
            if(empty($val['content']))      $this->outPut(ResponseCode::INVALID_PARAMETER, '投票选项内容不得为空');
            if(in_array($val['content'], $all_content))         $this->outPut(ResponseCode::INVALID_PARAMETER, '投票选项内容不得重复');
            $all_content[] = $val['content'];
            if(mb_strlen($val['content']) > self::SUBITEMS_LENGTH)    $this->outPut(ResponseCode::INVALID_PARAMETER, '投票选项最多50个字');
        }
        return $input;
    }
}
