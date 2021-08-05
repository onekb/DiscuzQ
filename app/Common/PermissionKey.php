<?php

namespace App\Common;

class PermissionKey
{
    const CREATE_THREAD = 'createThread';

    const THREAD_INSERT_IMAGE = 'thread.insertImage';
    const THREAD_INSERT_VIDEO = 'thread.insertVideo';
    const THREAD_INSERT_AUDIO = 'thread.insertAudio';
    const THREAD_INSERT_ATTACHMENT = 'thread.insertAttachment';
    const THREAD_INSERT_GOODS = 'thread.insertGoods';
    const THREAD_INSERT_PAY = 'thread.insertPay';
    const THREAD_INSERT_REWARD = 'thread.insertReward';
    const THREAD_INSERT_RED_PACKET = 'thread.insertRedPacket';
    const THREAD_INSERT_POSITION = 'thread.insertPosition';

    const THREAD_ALLOW_ANONYMOUS = 'thread.allowAnonymous';

    const VIEW_THREADS = 'viewThreads';
    const THREAD_REPLY = 'thread.reply';
    const THREAD_FREE_VIEW_POSTS = 'thread.freeViewPosts';
    const THREAD_FAVORITE = 'thread.favorite';
    const THREAD_LIKE_POSTS = 'thread.likePosts';
    const THREAD_HIDE = 'thread.hide';
    const THREAD_EDIT = 'thread.edit';
    const THREAD_EDIT_OWN = 'thread.editOwnThreadOrPost';
    const THREAD_HIDE_OWN = 'thread.hideOwnThreadOrPost';
    const THREAD_ESSENCE = 'thread.essence';
    const THREAD_STICKY = 'thread.sticky';

    const THREAD_VIEW_POSTS = 'thread.viewPosts';
    const THREAD_HIDE_POSTS = 'thread.hidePosts';

    const DIALOG_CREATE = 'dialog.create';
    const CREATE_INVITE_USER_SCALE = 'other.canInviteUserScale';

    const CASH_CREATE = 'cash.create';
    const ORDER_CREATE = 'order.create';
    const TRADE_PAY_ORDER = 'trade.pay.order';

    const WALLET_VIEW_LIST = 'wallet.viewList';
    const WALLET_LOGS_VIEW_LIST = 'wallet.logs.viewList';
    const CASH_VIEW_LIST = 'cash.viewList';
    const USER_VIEW = 'user.view';
    const USER_FOLLOW_CREATE = 'userFollow.create';

    const CREATE_THREAD_WITH_CAPTCHA = 'createThreadWithCaptcha';
    const PUBLISH_NEED_BIND_PHONE = 'publishNeedBindPhone';
}
