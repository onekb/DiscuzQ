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

namespace App\Common;

class CacheKey
{
    //记录首页各个分类的数据缓存
    public const LIST_THREAD_HOME_INDEX = 'list_thread_home_index_';

    //记录各个缓存的key值，便于数据更新的时候删除
    public const LIST_THREAD_KEYS = 'list_thread_keys';

    //记录
    public const THREAD_RESOURCE_BY_ID = 'thread_resource_by_id_';

    public const POST_RESOURCE_BY_ID = 'post_resource_by_id_';

    //记录用户是否新注册用户
    public const NEW_USER_LOGIN = 'new_user_login_';

    //符合智能排序条件的id数组
    public const LIST_SEQUENCE_THREAD_INDEX = 'list_sequences_thread_index';

    public const LIST_SEQUENCE_THREAD_INDEX_KEYS = 'list_sequences_thread_index_keys';

    public const API_FREQUENCE = 'api_frequence';

    public const LIST_CATEGORIES = 'list_categories';

    public const LIST_V2_THREADS = 'list_v2_threads';

    // 存储小程序通知模板数据
    public const NOTICE_MINI_PROGRAM_TEMPLATES = 'notice_mini_program_templates';
    public const AUTH_USER_PREFIX = 'auth_user_';

    public const CHECK_PAID_GROUP = 'check_paid_group_';

    public const SETTINGS = 'settings';

    public const GROUP_PERMISSIONS = 'group_permissions_';

}
