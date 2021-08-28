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

use Discuz\Base\DzqCache;

class CacheKey
{

    //微信相关重复登录加锁
    const WECHAT_FILE_LOCK = 'wechat_file_lock:';

    const APP_CACHE = 'APP_CACHE';//容器全局变量

    //记录首页各个分类的数据缓存
    const LIST_THREAD_HOME_INDEX = 'list_thread_home_index_';

    //记录各个缓存的key值，便于数据更新的时候删除
    const LIST_THREAD_KEYS = 'list_thread_keys';

    //记录
    const THREAD_RESOURCE_BY_ID = 'thread_resource_by_id_';

    const POST_RESOURCE_BY_ID = 'post_resource_by_id_';

    //记录用户是否新注册用户
    const NEW_USER_LOGIN = 'new_user_login_';

    //符合智能排序条件的id数组
    const LIST_SEQUENCE_THREAD_INDEX = 'list_sequences_thread_index';

    const LIST_SEQUENCE_THREAD_INDEX_KEYS = 'list_sequences_thread_index_keys';

    const API_FREQUENCE = 'api_frequence';

    const LIST_CATEGORIES = 'list_categories';

    const LIST_V2_THREADS = 'list_v2_threads';

    // 存储小程序通知模板数据
    const NOTICE_MINI_PROGRAM_TEMPLATES = 'notice_mini_program_templates';
    const AUTH_USER_PREFIX = 'auth_user_';

    const CHECK_PAID_GROUP = 'check_paid_group_';

    const SETTINGS = 'settings';

    const CATEGORIES = 'categories';

    const LIST_EMOJI = 'list_emoji';

    const LIST_GROUPS = 'list_groups';

    const GROUP_PERMISSIONS = 'group_permissions';

    const SEQUENCE = 'sequence';


    //默认的创建时间降序
    const LIST_THREADS_V3_CREATE_TIME = 'list_threads_v3_create_time';//filterId->pageId
    //智能排序，不参与筛选
    const LIST_THREADS_V3_SEQUENCE = 'list_threads_v3_sequence';//filterId->pageId

    //搜索发现页
    const LIST_THREADS_V3_SEARCH = 'list_threads_v3_search';//filterId->pageId

    //付费首页
    const LIST_THREADS_V3_PAID_HOMEPAGE = 'list_threads_v3_paid_homepage';//filterId->pageId


    //浏览数排序
    const LIST_THREADS_V3_VIEW_COUNT = 'list_threads_v3_view_count';//filterId->pageId
    //评论时间排序
    const LIST_THREADS_V3_POST_TIME = 'list_threads_v3_post_time';//filterId->pageId
    //关注排序
    const LIST_THREADS_V3_ATTENTION = 'list_threads_v3_attention';//filterId->pageId
    //个人中心复合数据排序
    const LIST_THREADS_V3_COMPLEX = 'list_threads_v3_complex';//filterId->pageId


    const LIST_THREADS_V3_USERS = 'list_threads_v3_users';//发帖用户存储 id
    const LIST_THREADS_V3_THREADS = 'list_threads_v3_threads';//帖子数据存储 id
    const LIST_THREADS_V3_ATTACHMENT = 'list_threads_v3_attachment';//帖子附件数据存储 id
    const LIST_THREADS_V3_VIDEO = 'list_threads_v3_video';//帖子视频文件存储 id
    const LIST_THREADS_V3_TAGS = 'list_threads_v3_tags';//帖子标签存储 thread_id
    const LIST_THREADS_V3_TOMS = 'list_threads_v3_toms';//帖子插件存储 thread_id
    const LIST_THREADS_V3_GROUP_USER = 'list_threads_v3_group_user';//用户组 user_id
    const LIST_THREADS_V3_SEARCH_REPLACE = 'list_threads_v3_search_replace';//替换标签、话题和艾特
    const LIST_THREADS_V3_POST_USERS = 'list_threads_v3_post_users';//帖子卡面底部的点赞支付摘要 thread_id
    const LIST_THREADS_V3_VOTES = 'list_threads_v3_votes';  //帖子投票
    const LIST_THREADS_V3_VOTE_SUBITEMS = 'list_threads_v3_vote_subitems';  //帖子投票选项

    const LIST_THREADS_V3_POSTS = 'list_threads_v3_posts:';//帖子正文数据存储 thread_id,【碎片化多文件存储】--切分成20个缓存文件
    const LIST_THREADS_V3_USER_PAY_ORDERS = 'list_threads_v3_user_pay_orders:';//用户付费贴订单信息 user_id->thread_id
    const LIST_THREADS_V3_USER_REWARD_ORDERS = 'list_threads_v3_user_reward_orders:';//打赏的订单信息 user_id->thead_id
    const LIST_THREADS_V3_POST_LIKED = 'list_threads_v3_post_liked:';//是否点赞 user_id->post_id
    const LIST_THREADS_V3_THREAD_USERS = 'list_threads_v3_thread_users:';//是否收藏 user_id->thread_id

    const MONITOR_SYSTEM_TASK = 'monitor_system_task:';//监听系统定时任务是否启动

    const IMG_UPLOAD_TMP_DETECT = 'img_upload_tmp_detect:';//检测临时文件是否为当前用户上传

    const  CRAWLER_SPLQUEUE_INPUT_DATA = 'crawler_splqueue_input_data:'; // 数据抓取/内容导入-入参缓存

    public static $fileStore = [
        self::LIST_THREADS_V3_POSTS => 20
    ];

    public static function delListCache()
    {
        DzqCache::delKey(CacheKey::CATEGORIES);
        DzqCache::delKey(CacheKey::LIST_THREADS_V3_CREATE_TIME);
        DzqCache::delKey(CacheKey::LIST_THREADS_V3_SEQUENCE);
        DzqCache::delKey(CacheKey::LIST_THREADS_V3_SEARCH);
        DzqCache::delKey(CacheKey::LIST_THREADS_V3_PAID_HOMEPAGE);
        DzqCache::delKey(CacheKey::LIST_THREADS_V3_VIEW_COUNT);
        DzqCache::delKey(CacheKey::LIST_THREADS_V3_POST_TIME);
        DzqCache::delKey(CacheKey::LIST_THREADS_V3_COMPLEX);
        DzqCache::delKey(CacheKey::LIST_THREADS_V3_ATTENTION);
    }

}
