<?php
/**
 * Created by IntelliJ IDEA.
 * User: buff
 * Date: 2019/1/4
 * Time: 14:51
 */

namespace bdk\app\common\service;

use bdk\app\common\model\User as UserModel;
use bdk\traits\Register;
use think\facade\Session as TpSession;

class Session
{
    use Register;

    /**
     * 设置登录时发送验证码的手机号码
     * @param $phoneNo
     */
    public function setLoginSendVerifyCodePhone($phoneNo): void
    {
        TpSession::set('loginSendVerifyCodePhone', $phoneNo);
    }

    /**
     * 获取登录时发送的验证的手机号码
     * @return string
     */
    public function getLoginSendVerifyCodePhone(): string
    {
        return TpSession::get('loginSendVerifyCodePhone') ?? '';
    }

    public function login(int $uid): void
    {
        TpSession::set('uid', $uid);
        $isAdminUser = false;
        try {
            $user        = UserModel::get($uid);
            $isAdminUser = $user ? $user->isAdminUser() : false;
        } catch (Exception $ex) {
            BuffLog::sqlException($ex);
            $isAdminUser = false;
        }
        if ($isAdminUser) {
            TpSession::set('isAdmin', true);
        }
    }

    public function logout(): void
    {
        TpSession::clear();
    }

}