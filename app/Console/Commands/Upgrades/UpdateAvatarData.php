<?php

/**
 * Discuz & Tencent Cloud
 * This is NOT a freeware, use is subject to license terms
 */

namespace App\Console\Commands\Upgrades;

use App\Models\User;
use Discuz\Console\AbstractCommand;

class UpdateAvatarData extends AbstractCommand
{
    /**
     * @var string
     */
    protected $signature = 'upgrade:avatar';

    /**
     * @var string
     */
    protected $description = 'Update user avatar information.';

    /**
     * {@inheritdoc}
     */
    public function handle()
    {
        $user = new User;

        $user->timestamps = false;

        $user->newQuery()
            ->where('avatar', 'like', 'https://%')
            ->update(['avatar' => User::query()->raw('CONCAT(\'cos://\', id, \'.png\')')]);

        $this->info('success');
    }
}
