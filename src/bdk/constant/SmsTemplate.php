<?php

/*
 * Author: buff <admin@buffge.com>
 * Created on : 2018-7-25, 11:18:22
 * QQ:1515888956
 */
namespace buffge\constant;

class SmsTemplate
{
    //通知商家准备游戏
    //{s10}商家您好，您的{s7}游戏已达到最低付款人数。请为客户准备游戏.
    const
            NOTIFY_SELLER_TEAM_HAS_MIN_PERSON_PAYED = 'tzsj';
    //通知客户付款
    //{s10}用户您好，您参加的{s7}组队已达到游戏人数。请及时进入推理笔记小程序付款。
    const
            NOTIFY_USER_PAY_FOR_TEAM = 'tzkhfk';
    //发送入驻验证码
    //尊敬的用户您好，感谢您入驻玩么，您的验证码是{s4}。(5分钟内有效)
    const
            SEND_ENTRY_VERIFY_CODE = 'fsrzyzm';
    //发送入驻成功
    //尊敬的{s10}商家您好，您的入驻申请已通过审核,赶快登录后台添加活动吧。
    const
            NOTIFY_SELLER_ENTRY_SUCCESS = 'fsrzcg';
    //通知管理员有商家入驻
    //{s10}商家正在申请入驻,赶快去审核吧。
    const
            NOTIFY_ADMIN_HAS_NEW_SELLER = 'tzglyyrrz';
    //通知管理员有商家添加了新活动
    //{s10}商家添加了{s10}游戏活动,赶快去审核吧。
    const
            NOTIFY_ADMIN_HAS_NEW_ACTIVITY = 'tzglyysjtjlxhd';
}
