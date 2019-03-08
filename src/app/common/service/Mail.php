<?php
/**
 * Created by IntelliJ IDEA.
 * User: buff
 * Date: 2019/1/3
 * Time: 14:05
 */

namespace bdk\app\common\service;

use bdk\app\common\model\Log as BuffLog;
use bdk\plug\mail\Driver;
use Exception;
use think\facade\Config;

class Mail extends Driver
{
    public static function __make()
    {
        $conf = Config::pull('mail');
        return new self($conf);
    }

    public function sendRegisterVerifyCode(string $addr, int $code): bool
    {
        $subject = '尚创汇注册验证码';
        $from    = [
            Config::get('mail.uname') => '尚创汇',
        ];
        $body    = "您好,你正在注册尚创汇,您的验证码是 {$code} ,15分钟有效";
        try {
            $res = $this->send($subject, $from, [$addr], $body);
            return $res === 1;
        } catch (Exception $ex) {
            BuffLog::sqlException($ex);
            return false;
        }
    }
}
