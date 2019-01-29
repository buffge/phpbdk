<?php
/**
 * Created by IntelliJ IDEA.
 * User: buff
 * Date: 2019/1/3
 * Time: 14:10
 */

namespace bdk\app\common\service;

use bdk\traits\Register;
use think\facade\Cache as TpCache;
use think\facade\Request;

class Cache
{
    use Register;

    /**
     * 获取当前ip用户邮箱登录的邮箱
     * @return string|null
     */
    public function getRequestIpEmailLoginEmail(): ?string
    {
        $reqIp = Request::ip();
        $email = TpCache::get('emailLoginEmail-' . $reqIp);
        return is_string($email) ? $email : null;
    }

    /**
     * 获取当前ip用户邮箱登录的验证码
     * @return string|null
     */
    public function getRequestIpEmailLoginVerifyCode(): ?string
    {
        $reqIp = Request::ip();
        $code  = TpCache::get('emailLoginVerifyCode-' . $reqIp);
        return is_string($code) ? $code : null;
    }

    /**
     * 设置当前ip用户邮箱登录的邮箱
     * @param string $email
     */
    public function setRequestIpEmailLoginEmail(string $email): void
    {
        $reqIp = Request::ip();
        TpCache::set('emailLoginEmail-' . $reqIp, $email, 60 * 15);
    }

    /**
     * 设置当前ip用户邮箱登录的验证码
     * @param string $code
     */
    public function setRequestIpEmailLoginVerifyCode(string $code): void
    {
        $reqIp = Request::ip();
        TpCache::set('emailLoginVerifyCode-' . $reqIp, $code, 60 * 15);
    }

    /**
     * 设置登录时的手机验证码
     * @param $phoneNo
     */
    public function setLoginPhoneVerifyCode($phoneNo): void
    {
        TpCache::set('loginPhoneVerifyCode', $phoneNo, 60 * 15);
    }

    /**
     * 获取登录时发送的手机验证码
     * @return string
     */
    public function getLoginPhoneVerifyCode(): string
    {
        return TpCache::get('loginPhoneVerifyCode') ?? '';
    }
}