<?php

/*
 * Author: buff <admin@buffge.com>
 * Created on : 2018-8-6, 14:55:30
 * QQ:1515888956
 */

namespace bdk\utils;

use app\common\model\Log as BuffLog;
use bdk\constant\Common as CommonConstant;
use Exception;
use stdClass;
use think\facade\Config as TpConf;

class Sms
{
    public static function send(string $phone, string $templateAlias, array $param): array
    {
        $smsConf    = TpConf::pull('sms');
        $driverName = $smsConf['driver'];
        $alias      = $smsConf[$driverName]['template']['alias'];
        $class      = "\\bdk\\plug\\sms\\{$driverName}\\Driver";
        $sms        = new $class;
        try {
            $logRecord             = [
                'level'    => BuffLog::LEVEL_INFO,
                'type'     => BuffLog::TYPE_SEND_SMS,
                'msg'      => '',
                'extra'    => [],
                'search_0' => 'success',
            ];
            $json                  = [
                'code' => CommonConstant::SUCCESS,
            ];
            $templateContent       = $smsConf[$driverName]['template'][$alias[$templateAlias]]['content'];
            $smsParam              = $param;
            $reqNo                 = date("YmdHis") . mt_rand(100000000, 999999999);
            $res                   = $sms->send($phone, $alias[$templateAlias], $smsParam, $reqNo);
            $logRecord['level']    = BuffLog::LEVEL_INFO;
            $logRecord['extra']    = [
                'sendInfo' => [
                    'phone'           => $phone,
                    'templateContent' => $templateContent,
                    'templateCode'    => $templateAlias,
                    'param'           => $smsParam,
                    'reqNo'           => $reqNo,
                ],
            ];
            $logRecord['msg']      = '发送短信成功';
            $logRecord['search_0'] = 'success';
            $logRecord['search_1'] = $phone;
            $logRecord['search_2'] = $templateAlias;
            $logRecord['search_3'] = $reqNo;
            $logRecord['search_4'] = json_encode($res, JSON_UNESCAPED_UNICODE);
            $logRecord['search_5'] = $res->Message;
            if (!$res instanceof stdClass) {
                $json['msg']           = "发送短信失败,接口返回数据不是正确的stdClass类型";
                $logRecord['msg']      = '发送短信失败';
                $logRecord['level']    = BuffLog::LEVEL_WARNING;
                $logRecord['search_0'] = 'falid';
            } else {
                if ($res->Code !== 'OK') {
                    $json['code']          = CommonConstant::DEFAULT_ERROR;
                    $json['msg']           = $res->Message;
                    $logRecord['msg']      = $res->Message;
                    $logRecord['level']    = BuffLog::LEVEL_WARNING;
                    $logRecord['search_0'] = 'falid';
                }
            }
            $log = new BuffLog;
            $log->addItem($logRecord);
        } catch (Exception $ex) {
            $json['code'] = CommonConstant::DEFAULT_ERROR;
            $json['msg']  = $ex->getMessage();
        }
        return $json;
    }
}
