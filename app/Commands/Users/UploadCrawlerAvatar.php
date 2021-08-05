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

namespace App\Commands\Users;

use App\Exceptions\UploadException;
use App\Models\User;
use App\Repositories\UserRepository;
use App\User\CrawlerAvatarUploader;
use App\Validators\AvatarValidator;
use Discuz\Auth\AssertPermissionTrait;
use Discuz\Auth\Exception\PermissionDeniedException;
use Intervention\Image\ImageManager;
use Psr\Http\Message\UploadedFileInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile as SymfonyUploadedFile;

class UploadCrawlerAvatar
{
    use AssertPermissionTrait;

    /**
     * @var int
     */
    public $userId;

    /**
     * @var UploadedFileInterface
     */
    public $avatar;

    /**
     * @var User
     */
    public $actor;

    public $tmpFile;

    /**
     * @var UserRepository
     */
    protected $users;

    /**
     * @var AvatarUploader
     */
    protected $uploader;

    /**
     * @var AvatarValidator
     */
    protected $validator;

    /**
     * @param int $userId The ID of the user to upload the avatar for.
     * @param UploadedFileInterface $avatar The avatar file to upload.
     * @param User $actor The user performing the action.
     */
    public function __construct($userId, UploadedFileInterface $avatar, User $actor, $tmpFile)
    {
        $this->userId = $userId;
        $this->avatar = $avatar;
        $this->actor = $actor;
        $this->tmpFile = $tmpFile;
    }

    /**
     * @param UserRepository $users
     * @param CrawlerAvatarUploader $uploader
     * @param AvatarValidator $validator
     * @return User|mixed
     * @throws PermissionDeniedException
     * @throws UploadException
     * @throws \Illuminate\Validation\ValidationException
     */
    public function handle(UserRepository $users, CrawlerAvatarUploader $uploader, AvatarValidator $validator)
    {
        $this->users = $users;
        $this->uploader = $uploader;
        $this->validator = $validator;

        return $this();
    }

    /**
     * @return mixed
     * @throws PermissionDeniedException
     * @throws UploadException
     * @throws \Illuminate\Validation\ValidationException
     */
    public function __invoke()
    {
        $user = User::query()->where('id',$this->userId)->first();
        $ext = pathinfo($this->avatar->getClientFilename(), PATHINFO_EXTENSION);
        $ext = $ext ? ".$ext" : '';

        $tmpFile = $this->tmpFile;
        $tmpFileWithExt = $tmpFile . $ext;

        try {
            $file = new SymfonyUploadedFile(
                $tmpFileWithExt,
                $this->avatar->getClientFilename(),
                $this->avatar->getClientMediaType(),
                $this->avatar->getError(),
                true
            );

            $this->validator->valid(['avatar' => $file]);

            $image = (new ImageManager())->make($tmpFileWithExt);

            $this->uploader->upload($user, $image);

            $user->save();
        } finally {
            @unlink($tmpFile);
            @unlink($tmpFileWithExt);
        }

        return $user;
    }
}
