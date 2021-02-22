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

namespace App\Console\Commands\Upgrades;

use App\Models\Attachment;
use App\Models\Order;
use App\Models\Post;
use App\Models\Thread;
use App\Models\Topic;
use App\Models\User;
use App\Repositories\PostRepository;
use App\Repositories\ThreadRepository;
use Discuz\Console\AbstractCommand;
use Discuz\Qcloud\QcloudTrait;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\ConsoleOutput;

class InitPostContent extends AbstractCommand
{
    use QcloudTrait;

    protected $signature = 'upgrade:postContent';

    protected $description = 'Initialize the post content, convert the original content to the json data of the block editor. Need to be performed before migration.';

    protected $posts;

    protected $threads;

    public function __construct(ThreadRepository $threads, PostRepository $posts)
    {
        parent::__construct();

        $this->threads = $threads;
        $this->posts = $posts;
        $this->posts = $posts;
    }

    public function handle()
    {
        /**
        {
            "blocks": [{
                "type": "pay",
                "data": {
                    "payid": "xxx",
                    "price": 100,
                    "defaultBlock": "0",
                    "child": [{
                        "type": "text",
                        "data": {
                            "value": "ooo  @yyyy #xxx# :oooo:  $oooooopoo$",
                            "userMention": [{
                                "id": "1",
                                "name": "@yyyy"
                            },{
                                "id": "2",
                                "name": "@xxx"
                            }],
                            "topic": [{
                                "id": "1",
                                "name": "#xxx#"
                            },{
                                "id": "2",
                                "name": "#yyy#"
                            }],
                            "emoji": [{
                                "name": ":oooo:",
                                "url": "http://xxxx"
                            }]
                        }
                    }]
                    }
                },
                {
                "type": "text",
                "data": {
                    "value": "Editor.js"
                }
                },
                {
                "type": "image",
                "data": {
                    "value": [1]
                }
                },
                {
                "type": "attachment",
                "data": {
                    "value": [1]
                }
                },
                {
                "type": "attachment",
                "data": {
                    "value": [1]
                }
                },
                {
                "type": "audio",
                "data": {
                    "value": [1]
                }
                },
                {
                "type": "video",
                "data": {
                "value": [1]
                }
                },
                {
                "type": "goods",
                "data": {
                    "value": [1]
                }
                },
                {
                "type": "vote",
                "data": {
                    "value": [1]
                }
                }
            ],
            "listBlock": "0"
        }
         */

        $output = new ConsoleOutput();
        $progressBar = new ProgressBar($output, $this->posts->query()->orderBy('id', 'asc')->count());
        $progressBar->setFormat("   %elapsed:6s%/%estimated:-6s%   内存消耗: %memory:6s%\n%current%/%max% [%bar%] %percent:3s%%");

        $progressBar->start();
        $this->posts->query()->orderBy('id')->chunk(1, function (Collection $data) use ($progressBar) {
            /** @var Post $post */
            $post = $data->first();
            $blocks = ['blocks'=>[], 'listBlock'=>0];
            /** 非付费块 */
            $baseBlocks = $this->setBlock($post);

            /** 付费（文本、图片、附件、视频）  */
            if ($post->is_first && $post->thread && $post->thread->price) {
                $payBlock['type'] = 'pay';
                $payBlock['price'] = $post->thread->price;
                $payBlock['child'][] = $baseBlocks['blocks'];
                $payBlock['blockPayid'] = Str::uuid();

                $payBlock['defaultBlock'] = $baseBlocks['listBlock'];

                array_push($blocks['blocks'], $payBlock);

                //已支付的用户订单增加块id
                Order::query()
                    ->where('thread_id', $post->thread_id)
                    ->where('status', Order::ORDER_STATUS_PAID)
                    ->where('type', Order::ORDER_TYPE_THREAD)
                    ->whereNull('block_payid')
                    ->update(['block_payid' => $payBlock['blockPayid']]);

            } else {
                $blocks = $baseBlocks;
            }
            $post->content_new = $blocks;
            $post->save();
            $progressBar->advance();
        });
        $progressBar->finish();
        echo "\n";
    }

    private function setBlock(Post $post)
    {
        $blocks = [];
        $listBlock = 0;
        /** 文本  */
        $textBlock['type'] = 'text';
        $textBlock['data']['value'] = $post->content;
        if ($post->is_first && $post->thread) {
            //话题、@
            if (!$post->thread->topic->isEmpty()) {
                $post->thread->topic->each(function (Topic $topic) {
                    $textBlock['data']['topic'][] = ['id'=>$topic->id, 'name'=>'#'.$topic->content.'#'];
                });
            }
            if (!$post->mentionUsers->isEmpty()) {
                $post->mentionUsers->each(function (User $user) {
                    $textBlock['data']['userMention'][] = ['id'=>$user->id, 'name'=>'@'.$user->username];
                });
            }
        }
        array_push($blocks, $textBlock);

        /** 图片  */
        if (!$post->images->isEmpty()) {
            $key = 0;
            $post->images->each(function (Attachment $image) use ($post,$blocks,&$key) {
                $imageBlock['type'] = 'image';
                $imageBlock['data']['value'] = [$image->uuid];

                $key = array_push($blocks, $imageBlock);
            });

            if ($post->thread->type = Thread::TYPE_OF_IMAGE) {
                $listBlock = $key;
            }
        }

        /** 附件  */
        if ($post->is_first && !$post->attachments->isEmpty()) {
            $key = 0;
            $post->images->each(function (Attachment $attachment) use ($post,$blocks,&$key) {
                $attachmentBlock['type'] = 'attachment';
                $attachmentBlock['data']['value'] = [$attachment->uuid];

                $key = array_push($blocks, $attachmentBlock);
            });

            if ($post->thread->type = Thread::TYPE_OF_IMAGE) {
                $listBlock = $key;
            }
        }

        /** 视频  */
        if ($post->is_first && $post->thread && !$post->thread->threadVideo->isEmpty()) {
            $videoBlock['type'] = 'video';
            $videoBlock['data']['value'] = [$post->thread->threadVideo->first()->id];

            $key = array_push($blocks, $videoBlock);

            if ($post->thread->type = Thread::TYPE_OF_IMAGE) {
                $listBlock = $key;
            }
        }

        return ['blocks'=>$blocks,'listBlock'=>$listBlock];
    }
}
