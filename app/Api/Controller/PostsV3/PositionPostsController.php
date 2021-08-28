<?php

namespace App\Api\Controller\PostsV3;

use App\Api\Controller\ThreadsV3\ThreadHelper;
use App\Api\Serializer\AttachmentSerializer;
use App\Api\Serializer\PostSerializer;
use App\Common\ResponseCode;
use App\Formatter\Formatter;
use App\Models\Attachment;
use App\Models\Group;
use App\Models\Post;
use App\Models\Thread;
use App\Models\User;
use App\Models\UserWalletLog;
use App\Providers\PostServiceProvider;
use App\Repositories\UserRepository;
use Discuz\Auth\Exception\PermissionDeniedException;
use Discuz\Base\DzqController;
use Illuminate\Contracts\Auth\Access\Gate;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Symfony\Component\Translation\Exception\NotFoundResourceException;

class PositionPostsController extends DzqController
{
    protected $post_ids;
    protected $postId;
    protected $posts;
    protected $pageSize;
    protected $is_reply;

    protected function checkRequestPermissions(UserRepository $userRepo)
    {
        $filters = $this->inPut('filter') ?: [];
        $threadId = Arr::get($filters, 'threadId');
        $this->postId = Arr::get($filters, 'postId');
        $this->pageSize = Arr::get($filters, 'pageSize');
        $this->is_reply = 0;

        if (empty($threadId) || empty($this->postId)) {
            return false;
        }

        $thread = Thread::query()
            ->where(['id' => $threadId])
            ->whereNull('deleted_at')
            ->first();

        $posts = Post::query()->find($this->postId);
        if(empty($posts)){
            $this->outPut(ResponseCode::RESOURCE_NOT_FOUND);
        }
        // 如果有评论id，说明是楼中楼评论
        if(!empty($posts->reply_post_id))       $this->is_reply = 1;
        if(empty($posts->reply_post_id) && empty($this->pageSize)){
            $this->outPut(ResponseCode::INVALID_PARAMETER, '一级评论缺少pageSize');
        }

        $where = [
            'thread_id' => $threadId,
            'is_first' => 0,
            'is_comment' => 0,
            'is_approved' => 1
        ];

        if($this->is_reply){
            $where['reply_post_id'] = $posts->reply_post_id;
            $where['is_comment'] = 1;
        }

        $this->posts = Post::query()
            ->where($where)
            ->whereNull('deleted_at')
            ->orderBy('created_at','asc')
            ->get()->map(function ($post) {
                return $this->getPost($post);
            });


        $this->post_ids = array_column($this->posts->toArray(), 'id');

        return $userRepo->canViewThreadDetail($this->user, $thread);
    }

    public function getPost($post){
        return [
            'id' => $post->id,
            'rewards' => floatval(sprintf('%.2f', $post->getPostReward(UserWalletLog::TYPE_INCOME_THREAD_REWARD)))
        ];
    }

    public function main(){
        $key = array_search($this->postId, $this->post_ids);
        $position = $key + 1;
        $page = 1;
        //这里判断，如果是一级评论，就需要分页，如果不是一级评论则不需要分页，获取的 position 就是评论位置
        if($this->is_reply){
            $location = $position;
        }else{
            $page = ceil($position/$this->pageSize);
            //具体的 location 还需要  rewards 参数
            $location_posts = $this->posts->slice(($page - 1) * $this->pageSize, $this->pageSize)->sortByDesc('rewards')->values()->toArray();
            $location_post_ids = array_column($location_posts, 'id');
            $location = array_search($this->postId, $location_post_ids) + 1;
        }

        $res_data = [
            'page'  =>  $page,
            'location'  =>  $location,
            'pageSize'  =>  $this->pageSize
        ];
        $this->outPut(ResponseCode::SUCCESS, '获取位置成功', $res_data);
    }

}
