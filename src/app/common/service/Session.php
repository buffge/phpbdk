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
     * 获取登录时发送的验证码的手机号码
     * @return string
     */
    public function getLoginSendVerifyCodePhone(): string
    {
        return TpSession::get('loginSendVerifyCodePhone') ?? '';
    }

    /**
     * 设置手机号码登录时发送的验证码
     * @param $phoneNo
     */
    public function setPhoneLoginSendVerifyCode(int $verifyCode): void
    {
        TpSession::set('loginPhoneSendVerifyCode', $verifyCode);
    }

    /**
     * 获取手机号码登录时发送的验证码
     * @return string
     */
    public function getPhoneLoginSendVerifyCode(): int
    {
        return TpSession::get('loginPhoneSendVerifyCode') ?? 0;
    }

    /**
     * 设置登录时发送验证码的邮箱
     * @param $phoneNo
     */
    public function setLoginSendVerifyCodeEmail(string $email): void
    {
        TpSession::set('loginSendVerifyCodeEmail', $email);
    }

    /**
     * 获取登录时发送的验证码的邮箱
     * @return string
     */
    public function getLoginSendVerifyCodeEmail(): string
    {
        return TpSession::get('loginSendVerifyCodeEmail') ?? '';
    }

    /**
     * 设置邮箱登录时发送的验证码
     * @param $phoneNo
     */
    public function setEmailLoginSendVerifyCode(int $verifyCode): void
    {
        TpSession::set('loginEmailSendVerifyCode', $verifyCode);
    }

    /**
     * 获取邮箱登录时发送的验证码
     * @return string
     */
    public function getEmailLoginSendVerifyCode(): int
    {
        return TpSession::get('loginEmailSendVerifyCode') ?? 0;
    }

    /**
     * 设置注册时发送验证码的邮箱
     * @param $phoneNo
     */
    public function setRegisterSendVerifyCodeEmail(string $email): void
    {
        TpSession::set('registerVerifyCodeEmail', $email);
    }

    /**
     * 获取注册时发送的验证码的邮箱
     * @return string
     */
    public function getRegisterSendVerifyCodeEmail(): string
    {
        return TpSession::get('registerVerifyCodeEmail') ?? '';
    }

    /**
     * 设置邮箱注册时发送的验证码
     * @param $phoneNo
     */
    public function setEmailRegisterSendVerifyCode(int $verifyCode): void
    {
        TpSession::set('registerEmailSendVerifyCode', (string)$verifyCode);
    }

    /**
     * 获取邮箱注册时发送的验证码
     * @return string
     */
    public function getEmailRegisterSendVerifyCode(): string
    {
        return (string)TpSession::get('registerEmailSendVerifyCode') ?? '0';
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
        if ( $isAdminUser ) {
            TpSession::set('isAdmin', true);
        }
    }

    public function logout(): void
    {
        TpSession::clear();
    }

}