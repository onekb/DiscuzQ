<?php

namespace App\Notifications\Messages\Sms;

use App\Models\Question;
use App\Models\Thread;
use App\Models\User;
use Discuz\Notifications\Messages\SimpleMessage;
use Illuminate\Contracts\Routing\UrlGenerator;

/**
 * 问答回答通知 - 微信
 * Class QuestionedAnswerSmsMessage
 *
 * @package App\Notifications\Messages\Sms
 */
class QuestionedAnswerSmsMessage extends SimpleMessage
{
    /**
     * @var Question $question
     */
    protected $question;

    /**
     * @var User $actor
     */
    protected $actor;

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
        [$firstData, $actor, $question] = $parameters;
        // set parent tpl data
        $this->firstData = $firstData;

        // 提问人 / 被提问人
        $this->actor = $actor;
        $this->question = $question;

        $this->template();
    }

    public function template()
    {
        return $this->getSmsContent();
    }

    protected function titleReplaceVars()
    {
        return [];
    }

    public function contentReplaceVars($data)
    {
        $threadTitle = $this->question->thread->getContentByType(Thread::CONTENT_LENGTH, true);
        $questionContent = $this->question->getContentFormat(Question::CONTENT_LENGTH, true);

        /**
         * 设置父类 模板数据
         * @parem $user_id 回答人用户ID (可用于跳转到用户信息)
         * @parem $user_name 回答人姓名
         * @parem $be_user_name 被提问人
         * @parem $question_content 回答的内容
         * @parem $question_price 提问价格
         * @parem $question_created_at 提问创建时间
         * @parem $question_expired_at 提问过期时间
         * @parem $thread_id 主题ID
         * @parem $thread_title 主题标题/首帖内容 (如果有title是title，没有则是首帖内容)
         */
        $this->setTemplateData([
            '{$user_id}'             => $this->actor->id,
            '{$user_name}'           => $this->actor->username,
            '{$be_user_name}'        => $this->question->beUser->username,
            '{$question_content}'    => $this->strWords($questionContent),
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
