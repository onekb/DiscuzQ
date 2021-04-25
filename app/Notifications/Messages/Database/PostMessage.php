<?php

namespace App\Notifications\Messages\Database;

use App\Models\Post;
use Discuz\Notifications\Messages\SimpleMessage;
use Illuminate\Support\Arr;

/**
 * Post 通知
 */
class PostMessage extends SimpleMessage
{
    protected $actor;

    protected $data;

    /**
     * @var Post
     */
    protected $post;

    public function __construct()
    {
        //
    }

    public function setData(...$parameters)
    {
        // 解构赋值
        [$firstData, $actor, $data] = $parameters;
        // set parent tpl data
        $this->firstData = $firstData;

        $this->actor = $actor;
        $this->data = $data;

        // set post model
        if (isset($this->data['post'])) {
            $this->post = $data['post'];
            $this->render();
        }
    }

    protected function titleReplaceVars()
    {
        return [];
    }

    public function contentReplaceVars($data)
    {
        $message = Arr::get($data, 'message', '');

        return [
            $this->post->user->username,
            $message,
            Arr::get($data, 'refuse', '无'),
        ];
    }

    public function render()
    {
        $postData = Arr::get($this->data, 'post');
        $build = [
            'title'     => $this->getTitle(),
            'content'   => $this->getContent($this->data),
            'raw'       => Arr::get($this->data, 'raw'),
            'thread_id' => $postData->thread_id
        ];

        Arr::set($build, 'raw.tpl_id', $this->firstData->id);

        return $build;
    }

}
