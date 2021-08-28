<?php

namespace App\Console\Commands;

use App\Api\Controller\AttachmentV3\AttachmentTrait;
use App\Api\Controller\Crawler\CrawlerTrait;
use App\Censor\Censor;
use App\Commands\Attachment\AttachmentUploader;
use App\Commands\Users\RegisterCrawlerUser as RegisterUser;
use App\Commands\Users\UploadCrawlerAvatar;
use App\Common\CacheKey;
use App\Crawler\Douban;
use App\Crawler\Tieba;
use App\Crawler\Weibo;
use App\Models\Attachment;
use App\Models\Category;
use App\Models\Post;
use App\Models\Thread;
use App\Models\ThreadTag;
use App\Models\ThreadTom;
use App\Models\ThreadTopic;
use App\Models\ThreadVideo;
use App\Models\Topic;
use App\Models\User;
use App\Modules\ThreadTom\TomConfig;
use App\Repositories\UserRepository;
use App\User\CrawlerAvatarUploader;
use App\Validators\AttachmentValidator;
use App\Validators\AvatarValidator;
use App\Validators\UserValidator;
use App\Traits\VideoCloudTrait;
use Carbon\Carbon;
use Discuz\Auth\Guest;
use Discuz\Console\AbstractCommand;
use Discuz\Contracts\Setting\SettingsRepository;
use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Contracts\Events\Dispatcher as Events;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Database\ConnectionInterface;
use Intervention\Image\ImageManager;
use Laminas\Diactoros\UploadedFile as RequestUploadedFile;
use Symfony\Component\HttpFoundation\File\UploadedFile as AttachmentUploadedFile;

class CreateCrawlerDataCommand extends AbstractCommand
{
    use VideoCloudTrait;

    use CrawlerTrait;

    use AttachmentTrait;

    protected $signature = 'crawlerdata:create';

    protected $description = '数据爬取，内容导入';

    protected $userRepo;

    protected $bus;

    protected $settings;

    protected $censor;

    protected $userValidator;

    protected $avatarValidator;

    protected $crawlerAvatarUploader;

    protected $attachmentValidator;

    protected $uploader;

    protected $image;

    protected $db;

    protected $filesystem;

    protected $events;

    private $platform;

    private $categoryId;

    private $topic;

    private $startCrawlerTime;

    private $lockPath;

    public function __construct(
        UserRepository      $userRepo,
        Dispatcher          $bus,
        SettingsRepository  $settings,
        Events              $events,
        Censor              $censor,
        UserValidator       $userValidator,
        AvatarValidator     $avatarValidator,
        AttachmentValidator $attachmentValidator,
        ImageManager        $image,
        ConnectionInterface $db,
        Filesystem          $filesystem)
    {
        parent::__construct();
        $this->userRepo         = $userRepo;
        $this->bus              = $bus;
        $this->settings         = $settings;
        $this->events           = $events;
        $this->censor           = $censor;
        $this->userValidator    = $userValidator;
        $this->avatarValidator  = $avatarValidator;
        $this->attachmentValidator = $attachmentValidator;
        $this->image               = $image;
        $this->db                  = $db;
        $this->filesystem          = $filesystem;
        $this->uploader              = new AttachmentUploader($this->filesystem , $this->settings);
        $this->crawlerAvatarUploader = new CrawlerAvatarUploader($this->censor, $this->filesystem , $this->settings);
    }

    public function handle()
    {
        $crawlerSplQueue = app('cache')->get(CacheKey::CRAWLER_SPLQUEUE_INPUT_DATA);
        if (!$crawlerSplQueue) {
            exit;
        }

        $publicPath = public_path();
        $this->lockPath = $publicPath . DIRECTORY_SEPARATOR . 'crawlerSplQueueLock.conf';
        if (file_exists($this->lockPath)) {
            $lockFileContent = $this->getLockFileContent($this->lockPath);
            if ($lockFileContent['runtime'] < Thread::CREATE_CRAWLER_DATA_LIMIT_MINUTE_TIME && $lockFileContent['status'] == Thread::IMPORT_PROCESSING) {
                $this->info('----The content import process has been occupied,You cannot start a new process.----');
                exit;
            } else if ($lockFileContent['runtime'] > Thread::CREATE_CRAWLER_DATA_LIMIT_MINUTE_TIME) {
                $this->insertLogs('----Execution timed out.The file lock has been deleted.----');
                app('cache')->clear();
                $this->changeLockFileContent($this->lockPath, 0, Thread::PROCESS_OF_START_INSERT_CRAWLER_DATA, Thread::IMPORT_TIMEOUT_ENDING, $lockFileContent['topic']);
                exit;
            }
        }

        $this->startCrawlerTime = Carbon::now();

        $data = [];
        while (!$crawlerSplQueue->isEmpty()) {
            $this->insertLogs('----Start importing crawler data.----');
            $inputData = $crawlerSplQueue->dequeue();
            $this->categoryId = $inputData['categoryId'];
            $this->platform = $inputData['platform'];
            $this->topic = $inputData['topic'];
            $this->changeLockFileContent($this->lockPath, $this->startCrawlerTime, Thread::PROCESS_OF_START_INSERT_CRAWLER_DATA, Thread::IMPORT_PROCESSING, $this->topic);

            $page = 1;
            if ($inputData['platform'] == Thread::CRAWLER_DATA_PLATFORM_OF_WEIBO) {
                $platform = new Weibo();
            } elseif ($inputData['platform'] == Thread::CRAWLER_DATA_PLATFORM_OF_TIEBA) {
                $platform = new Tieba();
            } elseif ($inputData['platform'] == Thread::CRAWLER_DATA_PLATFORM_OF_DOUBAN) {
                $platform = new Douban();
            }
            $pageData = $platform->main($inputData['topic'], $page);
            $this->insertLogs("----The " . $page . " page capture " . count($pageData) . " data'records.----");
            if (empty($pageData)) {
                $this->insertLogs('----No data is obtained. Process ends.----');
                app('cache')->clear();
                $this->changeLockFileContent($this->lockPath, $this->startCrawlerTime, Thread::PROCESS_OF_START_INSERT_CRAWLER_DATA, Thread::IMPORT_NOTHING_ENDING, $this->topic);
                exit;
            }
            $data = array_merge($data, $pageData);
            while (count($data) < $inputData['number'] && !empty($pageData)) {
                $page++;
                $pageData = $platform->main($inputData['topic'], $page);
                $this->insertLogs("----The " . $page . " page capture " . count($pageData) . " data'records.----");
                $data = array_merge($data, $pageData);
            }

            if (count($data) > $inputData['number']) {
                $data = array_slice($data,0,$inputData['number']);
            }

            $totalCount = count($data);
            $this->insertLogs('----The total number of crawler data: ' . $totalCount . '.----');

            $threads = array_column($data, 'forum');
            $threads = array_column($threads, null, 'id');

            $authors = array_column($data, 'user');
            $authors = array_column($authors, null, 'nickname');
            $comment = array_column($data, 'comment');
            $commentUsers = [];
            $commentLists = [];

            foreach ($comment as $key => $value) {
                $commentLists = array_merge($commentLists, $value);
                $commentUsers = array_merge($commentUsers, array_column($value, 'user'));
            }

            $commentUsers = array_column($commentUsers, null, 'nickname');
            $users = array_merge($authors, $commentUsers);

            $oldUsers = User::query()->select('id', 'username', 'nickname')->get()->toArray();
            $oldUsers = array_column($oldUsers, null, 'username');

            $oldTopics = Topic::query()->select('id', 'user_id', 'content', 'thread_count', 'view_count')->get()->toArray();
            $oldTopics = array_column($oldTopics, null, 'content');

            $this->changeLockFileContent($this->lockPath, $this->startCrawlerTime, Thread::PROCESS_OF_GET_CRAWLER_DATA, Thread::IMPORT_PROCESSING, $this->topic);
            $this->db->beginTransaction();
            try {
                $this->insertLogs("----Insert users'data start.----");
                $insertUsersResult = $this->insertUsers($oldUsers, $users);
                $this->insertLogs("----Insert users'data end.----");
                $this->changeLockFileContent($this->lockPath, $this->startCrawlerTime, Thread::PROCESS_OF_INSERT_USERS, Thread::IMPORT_PROCESSING, $this->topic);

                $this->insertLogs("----Insert threads'data start.----");
                [$insertThreadsResult, $oldTopics] = $this->insertThreads($oldTopics, $insertUsersResult, $threads);
                $this->insertLogs("----Insert threads'data end.----");
                $this->changeLockFileContent($this->lockPath, $this->startCrawlerTime, Thread::PROCESS_OF_INSERT_THREADS, Thread::IMPORT_PROCESSING, $this->topic);

                $this->insertLogs("----Insert posts'data start.----");
                $insertPostsResult = $this->insertPosts($insertUsersResult, $insertThreadsResult, $commentLists);
                $this->insertLogs("----Insert posts'data end.----");

                $this->db->commit();
                $this->insertLogs("----Importing crawler data success.The importing'data total number is " . count($insertThreadsResult) . ".----");
                $this->changeLockFileContent($this->lockPath, $this->startCrawlerTime, Thread::PROCESS_OF_INSERT_POSTS, Thread::IMPORT_PROCESSING, $this->topic);
            } catch (\Exception $e) {
                $this->db->rollBack();
                $this->insertLogs('----Importing crawler data fail,errorMsg: '. $e->getMessage() . '----');
                app('cache')->clear();
                $this->changeLockFileContent($this->lockPath, 0, Thread::PROCESS_OF_START_INSERT_CRAWLER_DATA, Thread::IMPORT_ABNORMAL_ENDING, $this->topic);
                exit;
            }
        }

        $updateThreadData = [];
        $updateTopicsIds = [];
        // 更新帖子-阅读数，回复数
        foreach ($insertPostsResult as $value) {
            if (isset($updateThreadData[$value['threadId']])) {
                $updateThreadData[$value['threadId']]['post_count']++;
                $updateThreadData[$value['threadId']]['view_count']++;
            } else {
                $updateThreadData[$value['threadId']]['threadId'] = $value['threadId'];
                $updateThreadData[$value['threadId']]['post_count'] = 1;
                $updateThreadData[$value['threadId']]['view_count'] = 1;
            }
        }

        // 更新话题下帖子数
        foreach ($insertThreadsResult as $value) {
            if (!empty($value['topicIds'])) {
                $updateTopicsIds = array_merge($updateTopicsIds, $value['topicIds']);
            }
        }

        $newThreadData = Thread::query()->whereIn('id', array_column($updateThreadData, 'threadId'))->get();
        $newThreadData->map(function ($item) use ($updateThreadData) {
            if (isset($updateThreadData[$item->id])) {
                $item->post_count = $item->post_count + $updateThreadData[$item->id]['post_count'];
                $item->view_count = $item->view_count + $updateThreadData[$item->id]['view_count'];
                $item->save();
            }
        });

        $newUserData = User::query()->whereIn('id', array_column($insertThreadsResult, 'userId'))->get();
        $newUserData->map(function ($item) {
            $query = Thread::query()
                ->where('user_id', $item->id)
                ->where('is_approved', Thread::APPROVED)
                ->where('is_draft', Thread::IS_NOT_DRAFT)
                ->whereNull('deleted_at')
                ->whereNotNull('user_id');
            $item->thread_count = $query->count();
            $item->save();
        });

        if (!empty($updateTopicsIds)) {
            $updateTopicsIds = array_flip($updateTopicsIds);
            $updateTopicsIds = array_keys($updateTopicsIds);
            $topicDatas = Topic::query()->whereIn('id' , $updateTopicsIds)->get();
            $topicDatas->map(function ($item) {
                $query = ThreadTopic::join('threads', 'threads.id', 'thread_topic.thread_id')
                    ->where('thread_topic.topic_id', $item->id)
                    ->where('threads.is_approved', Thread::APPROVED)
                    ->where('threads.is_draft', Thread::IS_NOT_DRAFT)
                    ->whereNull('threads.deleted_at')
                    ->whereNotNull('user_id');
                $item->thread_count = $query->count();
                $item->view_count = $query->sum('view_count');
                $item->save();
            });
        }
        Category::refreshThreadCountV3($this->categoryId);

        app('cache')->clear();
        $this->changeLockFileContent($this->lockPath, 0, Thread::PROCESS_OF_END_INSERT_CRAWLER_DATA, Thread::IMPORT_NORMAL_ENDING, $this->topic);
        exit;
    }

    /**
     * Insert Users'data
     */
    private function insertUsers($oldUsers, $users)
    {
        $insertUsersprogress = Thread::PROCESS_OF_GET_CRAWLER_DATA;
        $averageProgress = Thread::PROCESS_OF_GET_CRAWLER_DATA / count($users);
        foreach ($users as $key => $value) {
            $this->checkExecutionTime();
            try {
                $randomNumber = mt_rand(111111, 999999);
                $nickname = 'robotdzq_' . $value['nickname'];
                $password = $nickname . $randomNumber;
                $insertUsersprogress = $insertUsersprogress + $averageProgress;
                if (!isset($oldUsers[$nickname])) {
                    $data = [
                        'username' => $nickname,
                        'nickname' => $value['nickname'],
                        'password' => $password,
                        'passwordConfirmation' => $password,
                        'dataType' => 'crawler'
                    ];
                    $newGuest = new Guest();
                    $register = new RegisterUser($newGuest, $data);
                    $registerUserResult = $register->handle($this->events, $this->censor, $this->settings, $this->userValidator);
                    $this->insertLogs('----Insert a new user: ' . $registerUserResult->username . ' ,The user_id is ' . $registerUserResult->id .'.----');
                    $uploadAvatarResult = $this->uploadCrawlerUserAvatar($value, $registerUserResult);
                    if ($uploadAvatarResult) {
                        $uploadAvatarResult->status = User::STATUS_NORMAL;
                        $uploadAvatarResult->save();
                    }
                    $oldUsers = $oldUsers + [
                            $registerUserResult->username => [
                                'id'       => $registerUserResult->id,
                                'username' => $registerUserResult->username,
                                'nickname' => $registerUserResult->nickname
                            ]
                        ];
                }
                $this->changeLockFileContent($this->lockPath, $this->startCrawlerTime, $insertUsersprogress, Thread::IMPORT_PROCESSING, $this->topic);
            }catch (\Exception $e) {
                $this->insertLogs('----Insert a new user fail,errorMsg: '. $e->getMessage() . '----');
            }
        }
        return $oldUsers;
    }

    /**
     * Upload Users'avatar
     */
    private function uploadCrawlerUserAvatar($data, $registerUser)
    {
        if (strstr($data['avatar'], '?')) {
            $data['avatar'] = substr($data['avatar'], 0, strpos($data['avatar'], '?'));
        }

        // 头像下载至storage/tmp文件夹下
        if($this->platform == Thread::CRAWLER_DATA_PLATFORM_OF_TIEBA) {
            $data['avatar'] = $data['avatar'] . '.jpg';
        }

        $avatarData = parse_url($data['avatar']);
        $avatarData = pathinfo($avatarData['path']);

        set_time_limit(0);
        $file = $this->getFileContents($data['avatar']);
        if (!$file ) {
            return false;
        }
        $tmpFile = tempnam(storage_path('/tmp'), 'avatar');
        $ext = $avatarData['extension'];

        if (!in_array($ext, ['gif', 'png', 'jpg', 'jpeg', 'jpe', 'heic'])) {
            return false;
        }
        $ext = $ext ? ".$ext" : '';
        $tmpFileWithExt = $tmpFile . $ext;
        $avatarSize = @file_put_contents($tmpFileWithExt, $file);
        $mimeType = $this->getAttachmentMimeType($tmpFileWithExt);
        $avatarFile = new RequestUploadedFile(
            $tmpFile,
            $avatarSize,
            0,
            $avatarData['basename'],
            $mimeType
        );

        $avatar = new UploadCrawlerAvatar($registerUser->id, $avatarFile, $registerUser, $tmpFile);
        $uploadAvatarResult = $avatar->handle($this->userRepo, $this->crawlerAvatarUploader, $this->avatarValidator);
        return $uploadAvatarResult;
    }

    /**
     * Insert Threads'data
     */
    private function insertThreads($oldTopics, $users, $threads)
    {
        $threadIds = [];
        $insertThreadProgress = Thread::PROCESS_OF_INSERT_USERS;
        $averageProgress = Thread::PROCESS_OF_GET_CRAWLER_DATA / count($threads);
        // 写入帖子数据
        foreach ($threads as $key => $value) {
            $threadAuthor = 'robotdzq_' . $value['nickname'];
            $insertThreadProgress = $insertThreadProgress + $averageProgress;
            if (isset($users[$threadAuthor]) && !isset($threadIds[$value['id']])) {
                $this->checkExecutionTime();
                $this->insertLogs('----Match the user,insert a new thread start.----');
                $attachmentIds = [];
                $videoId = '';
                $topicIds = [];
                // 处理text中的img-src
                if ($this->platform != Thread::CRAWLER_DATA_PLATFORM_OF_WEIBO && preg_match("/<img.*>/", $value['text']['text'])) {
                    [$attachmentIds, $content] = $this->changeImg($users[$threadAuthor]['id'], $threads[$key]['text']['text'], Attachment::TYPE_OF_IMAGE);
                    $threads[$key]['text']['text'] = $content;
                } elseif ($this->platform == Thread::CRAWLER_DATA_PLATFORM_OF_WEIBO) {
                    $threads[$key]['text']['text'] = preg_replace('/<\s*img\s+[^>]*?src\s*=\s*(\'|\")(.*?)\\1[^>]*?\/?\s*>/i', '', $threads[$key]['text']['text']);
                }

                if (!empty($threads[$key]['text']['text'])) {
                    // 处理topic
                    if (!empty($value['text']['topic_list'])) {
                        foreach ($value['text']['topic_list'] as $k2 => $v2) {
                            if (isset($oldTopics[$v2])) {
                                $this->insertLogs("----Match the topic,don't need to create a new topic.----");
                                $topicIds[] = $oldTopics[$v2]['id'];
                                $html = sprintf('<span id="topic" value="%s">#%s#</span>', $oldTopics[$v2]['id'], $v2);
                                $topicContent = $oldTopics[$v2]['content'];
                            } else {
                                $this->insertLogs('----Insert a new topic.----');
                                $insertTopicResult = $this->insertTopic($users[$threadAuthor]['id'], $v2);
                                $topicIds[] = $insertTopicResult->id;
                                $html = sprintf('<span id="topic" value="%s">#%s#</span>', $insertTopicResult->id, $insertTopicResult->content);
                                $topicContent = $insertTopicResult->content;
                                $oldTopics = $oldTopics + [
                                        $insertTopicResult->content => [
                                            'id'      => $insertTopicResult->id,
                                            'user_id' => $insertTopicResult->user_id,
                                            'content' => $insertTopicResult->content
                                        ]
                                    ];
                            }

                            if (!strpos($threads[$key]['text']['text'], $html)){
                                $this->insertLogs("----Replace the topic'content in thread content.----");
                                $threads[$key]['text']['text'] = str_replace('#' . $topicContent . '#', $html, $threads[$key]['text']['text']);
                            }
                        }
                    }

                    // 处理图片:暂时只写入小图片，以后再优化
                    if (!empty($value['pics']['small_pics'])) {
                        $this->insertLogs("----Upload the thread'images attachment start.----");
                        $insertPicturesResult = $this->insertImages($users[$threadAuthor]['id'], $value['pics']['small_pics'], Attachment::TYPE_OF_IMAGE);
                        $attachmentIds = array_merge($attachmentIds, array_column($insertPicturesResult, 'id'));
                        $this->insertLogs("----Upload the thread'images attachment end.----");
                    }

                    // 写入帖子数据
                    $newThread = new Thread();
                    $newThread->user_id = $users[$threadAuthor]['id'];
                    $newThread->category_id = $this->categoryId;
                    $newThread->type = Thread::TYPE_OF_ALL;
                    $newThread->post_count = 1;
                    $newThread->share_count = mt_rand(0, 100);
                    $newThread->view_count = mt_rand(0, 100);
                    $newThread->address = $threads[$key]['text']['position'] ?? '';
                    $newThread->location = $threads[$key]['text']['position'] ?? '';
                    $newThread->is_draft = Thread::BOOL_NO;
                    $newThread->is_approved = Thread::BOOL_YES;
                    $newThread->is_anonymous = Thread::BOOL_NO;
                    $newThread->created_at = $newThread->updated_at = strtotime($threads[$key]['create_at']) ? $threads[$key]['create_at'] : Carbon::now();
                    $newThread->source = $this->platform;
                    $newThread->save();

                    // 处理视频，暂时只写入小视频，以后再优化
                    if (!empty($value['medias']['small_medias'])) {
                        if (isset($value['medias']['small_medias']['stream_url']) && !empty($value['medias']['small_medias']['stream_url'])) {
                            $this->insertLogs("----Upload the thread video start,the video url is [" . $value['medias']['small_medias']['stream_url'] . "].----");
                            $videoId = $this->videoUpload($newThread->user_id, $newThread->id, $value['medias']['small_medias']['stream_url'], $this->settings);
                            $this->insertLogs("----Upload the thread video end,the video_id is " . $videoId . ".----");
                        }
                    }

                    // 写入Tom,Tag
                    $insertTomResult = $this->insertTom($newThread, $attachmentIds, $videoId);

                    // 写入话题关联
                    if (!empty($topicIds)) {
                        $insertThreadTopicsResult = $this->insertThreadTopics($newThread, $topicIds);
                    }

                    // 写入帖子主体数据
                    $insertContentResult = $this->insertContent($newThread, $threads[$key]['text']['text']);
                    $this->insertLogs('----Insert a new thread end.The thread_id is ' . $newThread->id . '.----');
                    $threadIds = $threadIds + [
                        $threads[$key]['id'] => [
                            'threadId'  => $newThread->id,
                            'forumId'   => $threads[$key]['id'],
                            'userId'    => $newThread->user_id,
                            'topicIds'  => $topicIds,
                            'createdAt' => $threads[$key]['create_at']
                        ]
                    ];

                }
            }
            $this->changeLockFileContent($this->lockPath, $this->startCrawlerTime, $insertThreadProgress, Thread::IMPORT_PROCESSING, $this->topic);
        }

        return [$threadIds, $oldTopics];
    }

    /**
     * 对富文本信息中的数据
     * 匹配出所有的 <img> 标签的 src属性
     * @param string $contentStr 富文本字符串
     * @return array
     *
     */
    private function getImagesSrc($contentStr = "")
    {
        $imgSrcArr = [];
        //首先将富文本字符串中的 img 标签进行匹配
        $pattern_imgTag = '/<img\b.*?(?:\>|\/>)/i';
        preg_match_all($pattern_imgTag, $contentStr, $matchIMG);
        if (isset($matchIMG[0])) {
            foreach ($matchIMG[0] as $key => $imgTag) {
                //进一步提取 img标签中的 src属性信息
                $pattern_src = '/\bsrc\b\s*=\s*[\'\"]?([^\'\"]*)[\'\"]?/i';
                preg_match_all($pattern_src, $imgTag, $matchSrc);
                if (isset($matchSrc[1])) {
                    foreach ($matchSrc[1] as $src) {
                        //将匹配到的src信息压入数组
                        $imgSrcArr[] = $src;
                    }
                }
            }
        }
        return $imgSrcArr;
    }

    /**
     * Insert Images's data
     */
    private function insertImages($userId, $imagesSrc, $type)
    {
        $imageIds = [];
        $actor = User::query()->where('id', $userId)->first();
        $ipAddress = '';
        foreach ($imagesSrc as $key => $value) {
            $imageData = parse_url($value);
            $imageData = pathinfo($imageData['path']);
            set_time_limit(0);
            $file = $this->getFileContents($value);
            $imageSize = strlen($file);
            $maxSize = $this->settings->get('support_max_size', 'default', 0) * 1024 * 1024;
            if ($file && $imageSize > 0 && $imageSize < $maxSize) {
                ini_set('memory_limit',-1);
                $this->insertLogs("----Capture image's information.----");
                $tmpFile = tempnam(storage_path('/tmp'), 'attachment');
                $ext = $imageData['extension'];
                $ext = $ext ? ".$ext" : '';
                $tmpFileWithExt = $tmpFile . $ext;
                $putResult =  @file_put_contents($tmpFileWithExt, $file);
                if (!$putResult) {
                    return false;
                }
                $mimeType = $this->getAttachmentMimeType($tmpFileWithExt);

                $this->insertLogs('----ImageSize is:' . $imageSize . '.----');
                //上传临时目录之前验证
                $this->attachmentValidator->valid([
                    'type' => $type,
                    'file' => $file,
                    'size' => $imageSize,
                    'ext' => $imageData['extension'],
                ]);

                $imageFile = new AttachmentUploadedFile(
                    $tmpFileWithExt,
                    $imageData['basename'],
                    $mimeType,
                    0,
                    true
                );

                if(strtolower($ext) != 'gif'){
                    if ((int) $type === Attachment::TYPE_OF_IMAGE && extension_loaded('exif')) {
                        $this->image->make($tmpFileWithExt)->orientate()->save();
                    }
                }

                // 上传
                $this->uploader->uploadCrawlerData($imageFile, $type);

                list($width, $height) = getimagesize($tmpFileWithExt);

                $attachment = Attachment::build(
                    $actor->id,
                    $type,
                    $this->uploader->fileName,
                    $this->uploader->getPath(),
                    $imageFile->getClientOriginalName(),
                    $imageFile->getSize(),
                    $imageFile->getClientMimeType(),
                    $this->settings->get('qcloud_cos', 'qcloud') ? 1 : 0,
                    Attachment::APPROVED,
                    $ipAddress,
                    0,
                    $width,
                    $height
                );

                $attachment->save();
                @unlink($tmpFile);
                @unlink($tmpFileWithExt);

//            暂时无须拼接新src
//            if ($attachment->is_remote) {
//                $url = $this->settings->get('qcloud_cos_sign_url', 'qcloud', false)
//                    ? app()->make(Filesystem::class)->disk('attachment_cos')->temporaryUrl($attachment->full_path, Carbon::now()->addDay())
//                    : app()->make(Filesystem::class)->disk('attachment_cos')->url($attachment->full_path);
//            } else {
//                $url = app()->make(Filesystem::class)->disk('attachment')->url($attachment->full_path);
//            }
//
//            if(!empty($attachment->file_width) && !empty($attachment->file_height)){
//                if (strstr($url, $this->settings->get('qcloud_cos_bucket_name', 'qcloud'))) {
//                    if($this->settings->get('qcloud_cos_sign_url', 'qcloud', false)){          //开启了签名
//                        $imageUrl = $url . "&width=" . $attachment->file_width . "&" . "height=" . $attachment->file_height;
//                    }else{
//                        $imageUrl = $url . "?width=" . $attachment->file_width . "&" . "height=" . $attachment->file_height;
//                    }
//                } else {
//                    $imageUrl = $url . "?width=" . $attachment->file_width . "&" . "height=" . $attachment->file_height;
//                }
//            }

                    $imageIds[] = [
                        'id' => $attachment->id,
                        'oldImageSrc' => $value
//                      'newImageSrc' => $imageUrl ?? $url
                    ];

            }
        }

        return $imageIds;
    }

    /**
     * Insert Topics's data
     */
    private function insertTopic($userId, $topic)
    {
        $newTopic = new Topic();
        $newTopic->user_id = $userId;
        $newTopic->content = $topic;
        $newTopic->created_at = $newTopic->updated_at = Carbon::now();
        $newTopic->save();
        return $newTopic;
    }

    /**
    * Insert Thread Content
    */
    private function insertContent($thread, $content)
    {
        $threadPost = new Post();
        $threadPost->user_id = $thread->user_id;
        $threadPost->thread_id = $thread->id;
        $threadPost->content = $content;
        $threadPost->is_first = Post::FIRST_YES;
        $threadPost->is_approved = Post::APPROVED_YES;
        $threadPost->ip = '';
        $threadPost->port = 0;
        $threadPost->created_at = $threadPost->updated_at = $thread->created_at;
        $threadPost->save();
        return $threadPost;
    }

    /**
     * Insert Thread Tom&Tag
     */
    private function insertTom($thread, $attachmentIds, $videoId)
    {
        $attrs = [];
        $tags[] = [
            'thread_id' => $thread->id,
            'tag' => TomConfig::TOM_TEXT,
        ];

        if (!empty($attachmentIds)) {
            $attrs[] = [
                'thread_id' => $thread->id,
                'tom_type' => ThreadTag::IMAGE,
                'key' => ThreadTag::IMAGE,
                'value' => json_encode(['imageIds' => $attachmentIds], 256)
            ];

            $tags[] = [
                'thread_id' => $thread->id,
                'tag' => ThreadTag::IMAGE
            ];
        }

        if (!empty($videoId)) {
            $attrs[] = [
                'thread_id' => $thread->id,
                'tom_type' => ThreadTag::VIDEO,
                'key' => ThreadTag::VIDEO,
                'value' => json_encode(['videoId' => $videoId], 256)
            ];

            $tags[] = [
                'thread_id' => $thread->id,
                'tag' => ThreadTag::VIDEO
            ];

            ThreadVideo::query()->where('id', $videoId)->update(['thread_id' => $thread->id]);
        }

        ThreadTom::query()->insert($attrs);
        ThreadTag::query()->insert($tags);

        return true;
    }

    /**
     * Insert Thread Topics
     */
    private function insertThreadTopics($thread, $topicIds)
    {
        $threadTopic = [];
        foreach ($topicIds as $key => $value) {
            $threadTopic[] = [
                'thread_id'  => $thread->id,
                'topic_id'   =>  $value,
                'created_at' => $thread->created_at
            ];
        }
        $threadTopic = array_column($threadTopic, null, 'topic_id');
        $insertThreadTopicsResult = ThreadTopic::query()->insert($threadTopic);
        return $insertThreadTopicsResult;
    }

    /**
     * Insert Thread Posts'data
     */
    private function insertPosts($users, $threads, $posts)
    {
        $postIds = [];
        $insertPortsProcess = Thread::PROCESS_OF_INSERT_THREADS;
        $averageProgress = (Thread::PROCESS_OF_GET_CRAWLER_DATA - 1) / count($posts);
        foreach ($posts as $post) {
            $postAuthor = 'robotdzq_' . $post['user']['nickname'];
            $forumId = $post['comment']['forumId'];
            $insertPortsProcess = $insertPortsProcess + $averageProgress;
            if (isset($users[$postAuthor]) && isset($threads[$forumId])) {
                $this->checkExecutionTime();
                [$attachmentIds, $content] = $this->changeImg($users[$postAuthor]['id'], $post['comment']['text']['text'], Attachment::TYPE_OF_IMAGE);
                if (!empty($attachmentIds) || !empty($content)) {
                    $newPost = new Post();
                    $newPost->thread_id = $threads[$forumId]['threadId'];
                    $newPost->user_id = $users[$postAuthor]['id'];
                    $newPost->content = $content;
                    $newPost->is_first = Post::FIRST_NO;
                    $newPost->is_approved = Post::APPROVED_YES;
                    $newPost->ip = '';
                    $newPost->port = 0;
                    $newPost->created_at = $newPost->updated_at = strtotime($threads[$forumId]['createdAt']) ? $threads[$forumId]['createdAt'] : Carbon::now();
                    $newPost->save();

                    // 写入评论-图片关联
                    if (!empty($attachmentIds)) {
                        Attachment::query()->whereIn('id', $attachmentIds)->update(['type_id' => $newPost->id]);
                    }
                    $this->insertLogs('----Insert a new post end.The post_id is ' . $newPost->id . '.----');
                    $postIds = $postIds + [
                        $newPost->id => [
                            'threadId' => $threads[$forumId]['threadId'],
                            'postId' => $newPost->id,
                            'userId' => $newPost->user_id
                        ]
                    ];
                }
            }
            $this->changeLockFileContent($this->lockPath, $this->startCrawlerTime, $insertPortsProcess, Thread::IMPORT_PROCESSING, $this->topic);
        }
        return $postIds;
    }

    /**
     * Change Img
     */
    private function changeImg($userId, $content, $type)
    {
        $content = preg_replace('/(<img.*?)(style=.+?[\'|"])|((width)=[\'"]+[0-9]+[\'"]+)|((height)=[\'"]+[0-9]+[\'"]+)/i', '$1' , $content);
        preg_match_all("/<img[^>]+/", $content, $imagesSrc);
        if (!empty($imagesSrc) && !empty($imagesSrc[0])) {
            foreach ($imagesSrc[0] as $imageSrc) {
                if ($this->platform == Thread::CRAWLER_DATA_PLATFORM_OF_WEIBO &&
                    (strstr($imageSrc, 'emoticon') || strstr($imageSrc, 'timeline_card_small'))) {
                    $content = str_replace($imageSrc . '>', '', $content);
                }
            }
        }
        $postPicturesSrc = $this->getImagesSrc($content);
        $insertImagesResult = $this->insertImages($userId, $postPicturesSrc, $type);
        $insertImagesResult = array_column($insertImagesResult, null, 'oldImageSrc');
        $attachmentIds = array_column($insertImagesResult, 'id');
        if (!empty($attachmentIds)) {
            preg_match_all('/]*?src\s*=\s*(\'|\")(.*?)\\1[^>]*?\/?\s*>/i',$content,$oldImagesSrc);
            $oldImagesSrc = $oldImagesSrc[0];
            foreach ($oldImagesSrc as $src) {
                foreach ($insertImagesResult as $newSrc) {
                    if (strstr($src, $newSrc['oldImageSrc'])) {
                        $content = str_replace($src, "attachmentId='{$newSrc['id']}' tag='text-img'/>", $content);
                    }
                }
            }
        }
        return [$attachmentIds, $content];
    }

    private function checkExecutionTime()
    {
        $runTime = floor((time() - strtotime($this->startCrawlerTime))%86400/60);
        if ($runTime > Thread::CREATE_CRAWLER_DATA_LIMIT_MINUTE_TIME) {
            $this->insertLogs('----Execution timed out.The file lock has been deleted.----');
            app('cache')->clear();
            $this->changeLockFileContent($this->lockPath, 0, Thread::PROCESS_OF_START_INSERT_CRAWLER_DATA, Thread::IMPORT_TIMEOUT_ENDING, $this->topic);
            exit;
        }
    }

    private function insertLogs($logString)
    {
        $this->info($logString);
        app('log')->info($logString);
        return true;
    }
}