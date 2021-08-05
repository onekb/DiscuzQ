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
use App\Api\Controller as ApiController;

//删除用户和微信用户接口，上线前需去除
//$route->post('/user/delete', 'user.delete', ApiController\UsersV3\DeleteUserController::class);
//$route->post('/user/delete/wechat', 'user.delete.wechat', ApiController\UsersV3\UnbindWechatController::class);
$route->get('/models', 'models.get', ApiController\UsersV3\GetModelsController::class);

/*
|--------------------------------------------------------------------------
| 注册/登录
|--------------------------------------------------------------------------
*/
//二维码生成
$route->get('/users/pc/wechat/h5.genqrcode', 'pc.wechat.h5.qrcode', ApiController\UsersV3\WechatH5QrCodeController::class);
$route->get('/users/pc/wechat/miniprogram.genqrcode', 'pc.wechat.miniprogram.genqrcode', ApiController\UsersV3\MiniProgramQrcodeController::class);
$route->get('/users/pc/wechat.rebind.genqrcode', 'pc.wechat.rebind.genqrcode', ApiController\UsersV3\WechatPcRebindQrCodeController::class);
$route->get('/users/pc/wechat/h5.login', 'pc.wechat.h5.login.poll', ApiController\UsersV3\WechatPcLoginPollController::class);
$route->get('/users/pc/wechat/h5.bind', 'pc.wechat.h5.bind.poll', ApiController\UsersV3\WechatPcBindPollController::class);
$route->get('/users/pc/wechat/miniprogram.bind', 'pc.wechat.miniprogram.bind.poll', ApiController\UsersV3\MiniProgramPcBindPollController::class);
$route->get('/users/pc/wechat/miniprogram.login', 'pc.wechat.miniprogram.login.poll', ApiController\UsersV3\MiniProgramPcLoginPollController::class);
$route->get('/users/pc/wechat.rebind.poll', 'pc.wechat.rebind.poll', ApiController\UsersV3\WechatPcRebindPollController::class);
$route->get('/users/mobilebrowser/wechat/miniprogram.genscheme', 'pc.wechat.miniprogram.login.poll', ApiController\UsersV3\MiniProgramSchemeGenController::class);
//登录
$route->post('/users/username.login', 'username.login', ApiController\UsersV3\LoginController::class);
//注册
$route->post('/users/username.register', 'username.register', ApiController\UsersV3\RegisterController::class);
//控制用户名密码入口是否展示 -> 已迁移至forum接口
//$route->get('/users/username.login.isdisplay', 'username.login.isdisplay', ApiController\UsersV3\LsDisplayController::class);
//用户昵称检测
$route->post('/users/username.check', 'username.check', ApiController\UsersV3\CheckController::class);
//手机号（不区分端）
$route->post('/users/sms.send', 'sms.send', ApiController\UsersV3\SmsSendController::class);
$route->post('/users/sms.verify', 'sms.verify', ApiController\UsersV3\SmsVerifyController::class);
$route->post('/users/sms.login', 'sms.login', ApiController\UsersV3\SmsLoginController::class);
$route->post('/users/sms.bind', 'sms.bind', ApiController\UsersV3\SmsBindController::class);
$route->post('/users/sms.rebind', 'sms.rebind', ApiController\UsersV3\SmsRebindController::class);
$route->post('/users/sms.reset.pwd', 'sms.reset.pwd', ApiController\UsersV3\SmsResetPwdController::class);
//H5登录
$route->get('/users/wechat/h5.oauth', 'wechat.h5.oauth', ApiController\UsersV3\WechatH5OauthController::class);
$route->get('/users/wechat/h5.login', 'wechat.h5.login', ApiController\UsersV3\WechatH5LoginController::class);
$route->get('/users/wechat/h5.bind', 'wechat.h5.bind', ApiController\UsersV3\WechatH5BindController::class);
$route->get('/users/wechat/h5.rebind', 'wechat.h5.rebind', ApiController\UsersV3\WechatH5RebindController::class);
//小程序
$route->post('/users/wechat/miniprogram.login', 'wechat.miniprogram.login', ApiController\UsersV3\WechatMiniProgramLoginController::class);
$route->post('/users/wechat/miniprogram.bind', 'wechat.miniprogram.bind', ApiController\UsersV3\WechatMiniProgramBindController::class);
$route->post('/users/wechat/miniprogram.rebind', 'wechat.miniprogram.rebind', ApiController\UsersV3\WechatMiniProgramRebindController::class);
$route->get('/oauth/wechat/miniprogram/code', 'wechat.mini.program.code', ApiController\UsersV3\WechatMiniProgramCodeController::class);

//手机浏览器（微信外）登录并绑定微信
//$route->get('/users/mobilebrowser/wechat/h5.bind', 'mobilebrowser.wechat.h5.bind', ApiController\UsersV3\MiniProgramSchemeGenController::class);
//$route->post('/users/mobilebrowser/username.login', 'mobilebrowser.username.login', ApiController\UsersV3\MobileBrowserLoginController::class);
//$route->get('/users/mobilebrowser/wechat/miniprogram.bind', 'mobilebrowser.wechat.miniprogram.bind', ApiController\UsersV3\MiniProgramSchemeGenController::class);
//过渡开关打开微信绑定自动创建账号
$route->get('/users/wechat/transition/username.autobind', 'wechat.transition.username.autobind', ApiController\UsersV3\WechatTransitionAutoRegisterController::class);
$route->post('/users/wechat/transition/sms.bind', 'wechat.transition.sms.bind', ApiController\UsersV3\WechatTransitionBindSmsController::class);
//登录页设置昵称
$route->post('/users/nickname.set', 'users.nickname.set', ApiController\UsersV3\NicknameSettingController::class);
//前台扩展字段
// 查询扩展字段列表（用户注册后显示）
$route->get('/user/signinfields', 'user.signinfields.list', ApiController\SignInFieldsV3\ListUserSignInController::class);
// 用户首次提交扩展字段信息或者被驳回之后再次提交
$route->post('/user/signinfields', 'user.signinfields.create', ApiController\SignInFieldsV3\CreateUserSignInController::class);

//帖子查询
$route->get('/thread.detail','thread.detail',ApiController\ThreadsV3\ThreadDetailController::class);
$route->get('/thread.list','thread.list',ApiController\ThreadsV3\ThreadListController::class);
$route->get('/thread.stick','thread.stick',ApiController\ThreadsV3\ThreadStickController::class);
$route->get('/thread.likedusers','thread.likedusers',ApiController\ThreadsV3\ThreadLikedUsersController::class);
$route->get('/tom.detail','tom.detail',ApiController\ThreadsV3\SelectTomController::class);
$route->get('/thread.recommends','thread.recommends',ApiController\ThreadsV3\ThreadCommendController::class);

//帖子变更
$route->post('/thread.create','thread.create',ApiController\ThreadsV3\CreateThreadController::class);
$route->post('/thread.delete','thread.delete',ApiController\ThreadsV3\DeleteThreadController::class);
$route->post('/thread.update','thread.update',ApiController\ThreadsV3\UpdateThreadController::class);
$route->post('/tom.delete','tom.delete',ApiController\ThreadsV3\DeleteTomController::class);
$route->post('/tom.update','tom.update',ApiController\ThreadsV3\UpdateTomController::class);
$route->post('/thread/video', 'threads.video', ApiController\ThreadsV3\CreateThreadVideoController::class);

//首页配置接口
$route->get('/forum', 'forum.settings', ApiController\SettingsV3\ForumSettingsController::class);

$route->post('/thread.share','thread.share',ApiController\ThreadsV3\ThreadShareController::class);
$route->post('/goods/analysis', 'goods.analysis', ApiController\AnalysisV3\ResourceAnalysisGoodsController::class);

$route->get('/attachments', 'attachments.resource', ApiController\AttachmentV3\ResourceAttachmentController::class);
$route->post('/attachments', 'attachments.create', ApiController\AttachmentV3\CreateAttachmentController::class);
$route->get('/emoji', 'emoji.list', ApiController\EmojiV3\ListEmojiController::class);
$route->get('/follow', 'follow.list', ApiController\UsersV3\ListUserFollowController::class);
$route->post('/follow', 'follow.create', ApiController\UsersV3\CreateUserFollowController::class);
$route->post('/follow.delete', 'follow.delete', ApiController\UsersV3\DeleteUserFollowController::class);

$route->get('/groups.resource', 'groups.resource', ApiController\GroupV3\ResourceGroupsController::class);
$route->get('/topics.list', 'topics.list', ApiController\TopicV3\TopicListController::class);
$route->get('/users.list', 'users.list', ApiController\UsersV3\UsersListController::class);
$route->post('/order.create', 'order.create', ApiController\OrderV3\CreateOrderController::class);
$route->get('/order.detail', 'orders.resource.v2', ApiController\OrderV3\ResourceOrderController::class);
$route->post('/trade/notify/wechat', 'trade.notify.wechat', ApiController\TradeV3\Notify\WechatNotifyController::class);
$route->post('/trade/pay/order', 'trade.pay.order', ApiController\TradeV3\PayOrderController::class);
$route->get('/categories', 'categories', ApiController\CategoryV3\ListCategoriesController::class);
$route->get('/categories.thread', '/categories.thread', ApiController\CategoryV3\ListCategoriesThreadController::class);
$route->get('/posts', 'posts', ApiController\PostsV3\ListPostsController::class);
$route->post('/posts.update', 'posts.update', ApiController\PostsV3\UpdatePostController::class);
$route->post('/posts', 'posts', ApiController\PostsV3\CreatePostController::class);
$route->get('/posts.detail', 'posts.resource', ApiController\PostsV3\ResourcePostController::class);
$route->get('/posts.reply', 'posts.reply', ApiController\PostsV3\ResourcePostReplyController::class);

//用户
$route->post('/users/real', 'users.real', ApiController\UsersV3\RealUserController::class);
$route->get('/wallet/user', 'wallet.wallet', ApiController\WalletV3\ResourceUserWalletController::class);

/*
|--------------------------------------------------------------------------
| Notification
|--------------------------------------------------------------------------
*/
$route->get('/notification', 'notification.list', ApiController\NotificationV3\ListNotificationV2Controller::class);
$route->post('/notification.delete', 'notification.delete', ApiController\NotificationV3\DeleteNotificationV2Controller::class);
$route->get('/unreadnotification', 'unreadnotification.', ApiController\NotificationV3\UnreadNotificationController::class);



$route->get('/dialog', 'dialog.list', ApiController\DialogV3\ListDialogV2Controller::class);
$route->get('/dialog/message', 'dialog.message.list', ApiController\DialogV3\ListDialogMessageV2Controller::class);
$route->post('/dialog.create', 'dialog.create', ApiController\DialogV3\CreateDialogV2Controller::class);
$route->post('/dialog/message.create', 'dialog.message.create', ApiController\DialogV3\CreateDialogMessageV2Controller::class);
$route->post('/dialog.delete', 'dialog.delete', ApiController\DialogV3\DeleteDialogV2Controller::class);
$route->post('/dialog.update', 'dialog.update', ApiController\DialogV3\UpdateUnreadStatusController::class);
$route->get('/dialog.record', 'dialog.record', ApiController\DialogV3\DialogRecordController::class);

$route->post('/users/pay-password/reset', '', ApiController\UsersV3\ResetPayPasswordController::class);
$route->post('/users/update.user', 'users.update', ApiController\UsersV3\UpdateUsersController::class);


$route->get('/signature', 'signature', ApiController\QcloudV3\CreateVodUploadSignatureController::class);
$route->post('/threads/operate', 'threads.operate', ApiController\ThreadsV3\OperateThreadController::class);
$route->post('/posts.reward', 'posts.reward', ApiController\PostsV3\CreatePostRewardController::class);


//个人中心
$route->get('/wallet/log', 'wallet.log.list', ApiController\WalletV3\ListUserWalletLogsController::class);
$route->get('/wallet/cash', 'wallet.cash.list', ApiController\WalletV3\ListUserWalletCashController::class);
$route->post('/users/sms.reset.pay.pwd', 'sms.reset.pay.pwd', ApiController\UsersV3\SmsResetPayPwdController::class);
$route->post('/wallet/cash', 'wallet.cash.create', ApiController\WalletV3\CreateUserWalletCashController::class);
$route->get('/favorites', 'favorites', ApiController\ThreadsV3\ListFavoritesController::class);
$route->post('/users/background', 'user.upload.background', ApiController\UsersV3\UploadBackgroundController::class);
$route->get('/user', 'user.resource', ApiController\UsersV3\ProfileController::class);
$route->post('/users/update.mobile', 'update.mobile', ApiController\UsersV3\UpdateMobileController::class);
$route->post('/users/avatar', 'user.upload.avatar', ApiController\UsersV3\UploadAvatarController::class);


$route->get('/users/deny', 'user.deny.list', ApiController\UsersV3\ListDenyUserController::class);
$route->post('/users/deny', 'user.deny', ApiController\UsersV3\CreateDenyUserController::class);
$route->post('/users/deny.delete', 'user.delete.deny', ApiController\UsersV3\DeleteDenyUserController::class);

$route->get('/tom.permissions', 'tom.permissions', ApiController\GroupV3\TomPermissionsController::class);
$route->get('/threads.paid', 'threads.paid', ApiController\UsersV3\ListPaidThreadsController::class);


//待使用接口
$route->post('/reports', 'reports.create', ApiController\ReportV3\CreateReportsController::class);
$route->get('/redpacket.resource', 'redpacket.resource', ApiController\RedPacketV3\ResourceRedPacketController::class);

// 邀请invite
$route->get('/invite.users.list','invite.users.list',ApiController\InviteV3\InviteUsersListController::class);
$route->get('/invite.link.create','invite.link.create',ApiController\InviteV3\CreateInviteLinkController::class);

// 个人中心-站点信息-我的权限
$route->get('/group.permission.list', 'group.permission.list', ApiController\GroupV3\GroupPermissionListController::class);

//附件分享
$route->get('/attachment.share', 'attachment.share', ApiController\AttachmentV3\ShareAttachmentController::class);
$route->get('/attachment.download', '/attachment.download', ApiController\AttachmentV3\DownloadAttachmentController::class);

//生成jssdk签名
$route->get('/offiaccount/jssdk', 'offiaccount.jssdk', ApiController\WechatV3\OffIAccountJSSDKController::class);

$route->get('/thread.test', 'thread.test', ApiController\ThreadsV3\TestController::class);

$route->post('/open.api.log', 'open.api.log', ApiController\SettingsV3\OpenApiLogController::class);

$route->get('/view.count', 'view.count', ApiController\ThreadsV3\ViewCountController::class);
