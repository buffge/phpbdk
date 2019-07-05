<?php
/**
 * Created by IntelliJ IDEA.
 * User: buff
 * Date: 2019/3/12
 * Time: 15:27
 */

namespace bdk\app\common\service;

use think\facade\App;

class Sms
{
    public function sendSms(string $phone, string $tplCode, array $param = []): array
    {
        $smsDriver = App::get('smsDriver');
        return $smsDriver->sendSms($phone, $tplCode, $param);
    }
}