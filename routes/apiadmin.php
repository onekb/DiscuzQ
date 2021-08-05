<?php

use App\Api\Controller as ApiController;

$route->post('/login', 'login', ApiController\UsersV3\AdminLoginController::class);

$route->get('/reports', 'reports.list', ApiController\ReportV3\ListReportsController::class);
$route->post('/reports/batch', 'reports.batchUpdate', ApiController\ReportV3\BatchUpdateReportsController::class);
$route->post('/reports/delete', 'reports.batchDelete', ApiController\ReportV3\BatchDeleteReportsController::class);
$route->get('/settings', 'settings.list', ApiController\SettingsV3\ListSettingsController::class);
$route->post('/settings/logo', 'settings.upload.logo', ApiController\SettingsV3\UploadLogoController::class);
$route->post('/settings/delete.logo', 'settings.delete.logo', ApiController\SettingsV3\DeleteLogoController::class);
$route->get('/siteinfo', 'site.info', ApiController\SiteInfoV3Controller::class);
$route->post('/settings.create','settings.create',ApiController\SettingsV3\SetSettingsController::class);

//用户组
$route->post('/groups.create', 'groups.create', ApiController\GroupV3\CreateGroupController::class);
$route->get('/groups.list', 'groups.list', ApiController\GroupV3\ListGroupsController::class);
$route->post('/groups.batchupdate', 'groups.batchupdate', ApiController\GroupV3\BatchUpdateGroupController::class);
$route->post('/groups.batchdelete', 'groups.batchdelete', ApiController\GroupV3\BatchDeleteGroupsController::class);
$route->post('/users/update.user', 'users.admin', ApiController\UsersV3\UpdateAdminController::class);
$route->post('/users/examine', 'users.examine', ApiController\UsersV3\UpdateUsersStatusController::class);

// 财务
$route->get('/users.wallet.logs', 'users.wallet.logs', ApiController\WalletV3\UsersWalletLogsListController::class);
$route->get('/users.order.logs', 'users.order.logs', ApiController\OrderV3\UsersOrderLogsListController::class);
$route->get('/users.cash.logs', 'users.cash.logs', ApiController\WalletV3\UsersCashLogsListController::class);
$route->post('/wallet.cash.review', 'wallet.cash.review', ApiController\WalletV3\UserWalletCashReviewController::class);
$route->get('/statistic.finance', 'statistic.finance', ApiController\StatisticV3\FinanceProfileController::class);
$route->get('/statistic.financeChart', 'statistic.financeChart', ApiController\StatisticV3\FinanceChartController::class);
$route->get('/user.wallet', 'wallet.user', ApiController\WalletV3\ResourceUserWalletAdminController::class);
$route->post('/update.user.wallet', 'update.wallet.user', ApiController\WalletV3\UpdateUserWalletController::class);

//内容分类
$route->get('/categories', 'categories', ApiController\CategoryV3\AdminListCategoriesController::class);
$route->post('/categories.create', 'categories.create', ApiController\CategoryV3\CreateCategoriesController::class);
$route->post('/categories.update', 'categories.update', ApiController\CategoryV3\BatchUpdateCategoriesController::class);
$route->post('/categories.delete', 'categories.delete', ApiController\CategoryV3\BatchDeleteCategoriesController::class);

$route->post('/permission.update', 'permission.update', ApiController\PermissionV3\UpdateGroupPermissionController::class);

$route->get('/groups.resource', 'groups.resource', ApiController\GroupV3\ResourceGroupsController::class);
//注册扩展
$route->get('/signinfields', 'signinfields.list', ApiController\SignInFieldsV3\ListAdminSignInController::class);
$route->post('/signinfields', 'signinfields.create', ApiController\SignInFieldsV3\CreateAdminSignInController::class);
$route->get('/user/signinfields', 'user.signinfields.resource', ApiController\SignInFieldsV3\ResourceUserSignInController::class);

$route->post('/threads.batch', 'threads.batch', ApiController\ThreadsV3\BatchThreadsController::class);
//审核主题列表
$route->get('/manage.thread.list', 'manage.thread.list', ApiController\AdminV3\ManageThemeList::class);
//审核评论列表
$route->get('/manage.posts.list', 'manage.posts.list', ApiController\AdminV3\ManagePostList::class);
//提交审核
$route->post('/manage.submit.review', 'manage.review', ApiController\AdminV3\ManageSubmitReview::class);
//话题管理
$route->get('/topics.list', 'topics.list', ApiController\TopicV3\AdminTopicListController::class);
$route->post('/topics.batch.update', 'topics.batch.update', ApiController\TopicV3\BatchUpdateTopicController::class);
$route->post('/topics.batch.delete', 'topics.batch.delete', ApiController\TopicV3\BatchDeleteTopicController::class);

$route->get('/statistic/firstChart', 'statistic/firstChart', ApiController\StatisticV3\FirstChartController::class);

//用户
$route->get('/export/users', 'export.users', ApiController\UsersV3\ExportUserController::class);
$route->post('/users/avatar', 'user.upload.avatar', ApiController\UsersV3\UploadAvatarsController::class);
$route->post('/delete/users/avatar', 'user.upload.avatar', ApiController\UsersV3\DeleteAvatarController::class);
$route->get('/users', 'users.list', ApiController\UsersV3\ListUserScreenController::class);
$route->get('/user', 'user.resource', ApiController\UsersV3\ProfileController::class);

//内容过滤
$route->post('/stopwords.batch', 'stopwords.batch', ApiController\StopWordsV3\BatchCreateStopWordsController::class);
$route->get('/stopwords.list', 'stopwords.list', ApiController\StopWordsV3\ListStopWordsController::class);
$route->post('/stopwords.delete', 'stopwords.delete', ApiController\StopWordsV3\DeleteStopWordController::class);

//管理端站点设置
$route->get('/forum', 'forum.settings', ApiController\SettingsV3\ForumSettingsController::class);

//消息模板
$route->get('/notification/tpl', 'notification.tpl.list', ApiController\NotificationV3\ListNotificationTplV3Controller::class);
$route->get('/notification/tpl/detail', 'notification.tpl.detail', ApiController\NotificationV3\ResourceNotificationTplV3Controller::class);
$route->post('/notification/tpl/update', 'notification.tpl.update', ApiController\NotificationV3\UpdateNotificationTplV3Controller::class);

$route->get('/cache.delete', 'cache.delete', ApiController\CacheV3\DeleteCacheController::class);
$route->get('/sequence', 'sequence.list', ApiController\SettingsV3\ListSequenceController::class);
$route->post('/sequence', 'sequence', ApiController\SettingsV3\UpdateSequenceController::class);
$route->post('/refresh.token', 'refresh.token', ApiController\Oauth2V3\RefreshTokenController::class);

$route->get('/recommend.users', 'recommend.users', ApiController\Recommend\RecommendedUserListController::class);
$route->get('/recommend.topics', 'recommend.topics', ApiController\Recommend\RecommendedTopicListController::class);

// 判断是否已配置腾讯云  CheckQcloudController
$route->get('/checkQcloud', 'checkQcloud',  ApiController\CheckQcloudV3Controller::class);

//邀请朋友生成code
$route->get('/adminInvite.link.create','invite.link.create',ApiController\InviteV3\CreateInviteLinkAdminController::class);
$route->get('/stopWords/export', 'stopWords.export', ApiController\StopWordsV3\ExportStopWordsController::class);

//监听定时任务
$route->get('/monitor/system/task', 'monitor.system.task', ApiController\System\MonitorSystemTaskController::class);

