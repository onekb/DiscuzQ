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

namespace App\Models;

use App\Formatter\DialogMessageFormatter;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property int $user_id
 * @property int $attachment_id
 * @property int $dialog_id
 * @property int $message_text
 * @property Carbon $updated_at
 * @property Carbon $created_at
 * @package App\Models
 */
class DialogMessage extends Model
{
    const SUMMARY_LENGTH = 40;

    const SUMMARY_END_WITH = '...';

    /**
     * {@inheritdoc}
     */
    protected $table = 'dialog_message';

    /**
     * {@inheritdoc}
     */
    protected $dates = [
        'updated_at',
        'created_at',
    ];

    /**
     * The text formatter instance.
     *
     * @var DialogMessageFormatter
     */
    protected static $formatter;

    /**
     * @var array
     */
    protected $fillable = [];

    public function getMessageTextAttribute($value)
    {
        $value = json_decode(stripslashes($value));
        $value['message_text'] = static::$formatter->unparse($value['message_text']);
        $value = json_encode($value);

        return $value;
    }

    public function getCommonMessageText(){
        $message_text_old = $this->attributes['message_text'] ?: '';
        $message_text = json_decode(stripslashes($message_text_old));
        if (!empty($message_text)) {
            $messageText = $message_text->message_text;
        } else {
            $messageText = $this->attributes['message_text'];
        }

        return $messageText;
    }

    public function getParsedMessageTextAttribute()
    {
        return $this->getCommonMessageText();
    }

    public function setMessageTextAttribute($value)
    {
        $value['message_text'] = $value['message_text'] ? static::$formatter->parse($value['message_text'], $this) : null;
        $this->attributes['message_text'] = addslashes(json_encode($value));
    }

    public function setParsedMessageTextAttribute($value)
    {
        $message = [
            'message_text'  => $value,
            'image_url'     => ''
        ];
        $this->attributes['message_text'] = addslashes(json_encode($message));
    }

    public function getSummaryAttribute()
    {
        $messageText = $this->getCommonMessageText();
        $message_text = Str::of($messageText ?: '');

        if ($message_text->length() > self::SUMMARY_LENGTH) {
            $message_text = static::$formatter->parse(
                $message_text->substr(0, self::SUMMARY_LENGTH)->finish(self::SUMMARY_END_WITH)
            );
            $message_text = static::$formatter->render($message_text);
        } else {
            $message_text = $this->formatMessageText();
        }

        return str_replace('<br>', '', $message_text);
    }

    public function getMessageText()
    {
        return $this->getCommonMessageText();
    }

    public function formatMessageText()
    {
        $message_text_old = $this->attributes['message_text'] ?: '';
        $message_text = json_decode(stripslashes($message_text_old));
        if (!empty($message_text)) {
            $messageText = $message_text->message_text ? static::$formatter->render($message_text->message_text) : '';
        } else {
            $messageText = $message_text_old ? static::$formatter->render($message_text_old) : '';
        }

        return $messageText;
    }

    public function getImageUrlMessageText()
    {
        $message_text_old = $this->attributes['message_text'] ?: '';
        $message_text = json_decode(stripslashes($message_text_old));
        if (!empty($message_text)) {
            $messageText = $message_text->image_url;
        } else {
            $messageText = '';
        }

        return $messageText;
    }

    public static function build($user_id, $dialog_id, $attachment_id, $message_text)
    {
        $dialogMessage = new static();

        $dialogMessage->user_id       = $user_id;
        $dialogMessage->dialog_id     = $dialog_id;
        $dialogMessage->attachment_id = $attachment_id;
        $dialogMessage->message_text  = $message_text;

        return $dialogMessage;
    }

    public static function setFormatter(DialogMessageFormatter $formatter)
    {
        static::$formatter = $formatter;
    }

    public static function getFormatter()
    {
        return static::$formatter;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function attachment()
    {
        return $this->belongsTo(Attachment::class)->where('type', Attachment::TYPE_OF_DIALOG_MESSAGE);
    }
}
