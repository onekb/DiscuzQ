<?php

namespace App\Notifications\Messages\MiniProgram;

use App\Models\Post;
use App\Models\Thread;
use Discuz\Notifications\Messages\SimpleMessage;
use Illuminate\Contracts\Routing\UrlGenerator;

/**
 * Class LikedMiniProgramMessage
 * (内容点赞通知)
 *
 * @package App\Notifications\Messages\MiniProgram
 */
class LikedMiniProgramMessage extends SimpleMessage
{
    protected $actor;

    /**
     * @var Post
     */
    protected $post;

    /**
     * @var UrlGenerator
     */
    protected $url;

    public function __construct(UrlGenerator $url)
    {
        $this->url = $url;
    }

    public function setData(...$parameters)
    {
        [$firstData, $actor, $post] = $parameters;
        // set parent tpl data
        $this->firstData = $firstData;

        $this->actor = $actor;
        $this->post = $post;

        $this->template();
    }

    public function template()
    {
        return ['content' => $this->getMiniProgramContent()];
    }

    protected function titleReplaceVars()
    {
        return [];
    }

    public function contentReplaceVars($data)
    {
        $threadTitle = $this->post->thread->getContentByType(Thread::CONTENT_LENGTH, true);

        /**
         * 设置父类 模板数据
         * @parem $user_id 点赞人用户ID
         * @parem $user_name 点赞人姓名
         * @parem $thread_id 主题ID （可用于跳转参数）
         * @parem $thread_title 主题标题/首帖内容 (如果有title是title，没有则是首帖内容)
         * @parem $post_content 帖子内容
         */
        $this->setTemplateData([
            '{$user_id}'      => $this->actor->id,
            '{$user_name}'    => $this->actor->username,
            '{$thread_id}'    => $this->post->thread->id,
            '{$thread_title}' => $this->strWords($threadTitle),
            '{$post_content}' => $this->strWords($this->post->content),
        ]);

        // build data
        return $this->compiledArray();
    }

}
