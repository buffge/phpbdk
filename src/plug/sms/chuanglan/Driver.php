<?php

/*
 * Author: buff <admin@buffge.com>
 * Created on : 2018-7-2, 16:14:17
 * QQ:1515888956
 */

namespace buffge\plug\sms\chuanglan;

use buffge\constant\Common as CommonConstant;
use Exception;

class Driver
{
    protected $sendUrl;
    protected $account;
    protected $pwd;

    public function __construct(array $conf)
    {
        $this->sendUrl  = $conf['send_url'];
        $this->account  = $conf['account'];
        $this->pwd      = $conf['pwd'];
        $this->template = $conf['template'];
    }

    public function sendSms(string $phone, string $templateCode, array $param, bool $needstatus = true)
    {
        $msg      = $this->parseMsg($templateCode, $param);
        $postData = [
            'account'  => $this->account,
            'password' => $this->pwd,
            'msg'      => rawurlencode($msg),
            'phone'    => $phone,
            'report'   => $needstatus,
        ];
        return $this->curlPost($postData);
    }

    protected function parseMsg(string $templateCode, array $param)
    {
        if (!key_exists($templateCode, $this->template)) {
            throw new Exception("{$templateCode}不是有效的模板代码", CommonConstant::INVAILD_PARAM);
        }
        $templateText = $this->template[$templateCode];
        $templateText = preg_replace_callback('~{s(?<maxLength>\d+)}~', function ($matches) use (&$param) {
            $str = mb_substr(array_shift($param), 0, $matches['maxLength']);
            if (!$str) {
                throw new Exception("模板替换参数个数不符", CommonConstant::DEFAULT_ERROR);
            }
            return $str;
        }, $templateText);
        return $templateText;
    }

    /**
     * 通过CURL发送HTTP请求
     * @param array $postData //请求参数
     * @return mixed
     */
    protected function curlPost(array $postData)
    {
        $postData = json_encode($postData);
        $ch       = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->sendUrl);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json; charset=utf-8']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($ch, CURLOPT_TIMEOUT, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        $resp = curl_exec($ch);
        $res  = [
            'code' => CommonConstant::SUCCESS,
            'msg'  => '',
            'data' => [
            ],
        ];
        if (false == $resp) {
            $res['code']             = CommonConstant::DEFAULT_ERROR;
            $res['msg']              = curl_error($ch);
            $res['data']['curlInfo'] = json_encode(curl_getinfo($ch));
        } else {
            $res['data']['resp'] = $resp;
        }
        curl_close($ch);
        return $res;
    }
}
