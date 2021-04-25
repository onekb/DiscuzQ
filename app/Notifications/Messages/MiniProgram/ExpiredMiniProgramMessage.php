<?php

namespace App\Notifications\Messages\MiniProgram;

use App\Models\Question;
use App\Models\Thread;
use App\Models\User;
use Discuz\Notifications\Messages\SimpleMessage;
use Illuminate\Contracts\Routing\UrlGenerator;

/**
 * 过期通知 - 小程序
 *
 * @package App\Notifications\Messages\MiniProgram
 */
class ExpiredMiniProgramMessage extends SimpleMessage
{
    /**
     * @var Question $question
     */
    protected $question;

    /**
     * @var User $user
     */
    protected $user;

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
        [$firstData, $user, $question] = $parameters;
        // set parent tpl data
        $this->firstData = $firstData;

        $this->user = $user;
        $this->question = $question;

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
        $threadTitle = $this->question->thread->getContentByType(Thread::CONTENT_LENGTH, true);

        /**
         * 设置父类 模板数据
         * @parem $user_id 提问人用户ID (可用于跳转到用户信息)
         * @parem $user_name 提问人
         * @parem $be_user_name 被提问人
         * @parem $question_price 提问价格 (解冻金额)
         * @parem $question_created_at 提问创建时间
         * @parem $question_expired_at 提问过期时间
         * @parem $thread_id 主题ID
         * @parem $thread_title 主题标题/首帖内容 (如果有title是title，没有则是首帖内容)
         */
        $this->setTemplateData([
            '{$user_id}'             => $this->question->user->id,
            '{$user_name}'           => $this->question->user->username,
            '{$be_user_name}'        => $this->question->beUser->username,
            '{$question_price}'      => $this->question->price,
            '{$question_created_at}' => $this->question->created_at,
            '{$question_expired_at}' => $this->question->expired_at,
            '{$thread_id}'           => $this->question->thread_id,
            '{$thread_title}'        => $this->strWords($threadTitle),
        ]);

        // build data
        return $this->compiledArray();
    }

}
