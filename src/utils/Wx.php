<?php

/*
 * Author: buff <admin@buffge.com>
 * Created on : 2018-8-3, 14:35:01
 * QQ:1515888956
 */

namespace bdk\utils;

use Exception;
use think\facade\Cache;
use think\facade\Config as TpConf;
use think\facade\Request;
use think\facade\Session;

/**
 * 微信的一些常用操作
 */
class Wx
{
    /**
     *
     * @param bool $useCache
     * @return string
     */
    public static function getAccessToken(bool $useCache = true): string
    {
        if ($useCache) {
            if (Cache::has('accessToken')) {
                return Cache::get('accessToken');
            } else {
                $accessToken = self::getAccessToken(false);
                Cache::set('accessToken', $accessToken, 110 * 60);
                return $accessToken;
            }
        } else {
            $wxConf         = TpConf::pull('wx');
            $tokenAccessUrl = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=" .
                $wxConf['appid'] . "&secret=" . $wxConf['secret'];
            $res            = file_get_contents($tokenAccessUrl);
            $result         = json_decode($res, true);
            $access_token   = $result['access_token'];
            return $access_token;
        }
    }

    /**
     *
     * @param string $accessToken
     * @param bool $useCache
     * @return string
     * @throws Exception
     */
    public static function getJsApiTicket(string $accessToken, bool $useCache = true): string
    {
        if ($useCache) {
            if (Cache::has('jsApiTicket')) {
                return Cache::get('jsApiTicket');
            } else {
                $jsApiTicket = self::getJsApiTicket($accessToken, false);
                Cache::set('jsApiTicket', $jsApiTicket, 110 * 60);
                return $jsApiTicket;
            }
        } else {
            $tokenAccessUrl = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token={$accessToken}&type=jsapi";
            $res            = file_get_contents($tokenAccessUrl);
            $result         = json_decode($res, true);
            if ($result['errcode'] !== 0) {
                throw new Exception($result['errmsg']);
            }
            $jsApiTicket = $result['ticket'];
            return $jsApiTicket;
        }
    }

    /**
     * 微信签名
     * @param array $data
     * @return string
     */
    public static function sign(array $data): string
    {
        ksort($data);
        $oriStr = '';
        foreach ($data as $k => $v) {
            $oriStr .= "{$k}={$v}&";
        }
        $resStr = substr($oriStr, 0, -1);
        return sha1($resStr);
    }

    public static function getOauth2Info(): array
    {
        if (!Session::has('oauth2Info')) {
            $wxConf = TpConf::pull('wx');
            $appId  = $wxConf['appid'];
            $secret = $wxConf['secret'];
            if (Request::has('code', 'get')) {
                $code   = Request::get('code');
                $res    = file_get_contents("https://api.weixin.qq.com/sns/oauth2/access_token?appid={$appId}&secret={$secret}&code={$code}&grant_type=authorization_code");
                $resArr = json_decode($res, true);
                if (key_exists('errcode', $resArr)) {
                    throw new Exception($resArr['errmsg']);
                }
                if (!key_exists('openid', $resArr) ||
                    !key_exists('access_token', $resArr) ||
                    !key_exists('refresh_token', $resArr)) {
                    throw new Exception("获取用户信息失败,不存在openid或access_token或refresh_token");
                }
                Session::set('oauth2Info', $resArr);
                return $resArr;
            } else {
                $url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid="
                    . "{$appId}&redirect_uri=" . Request::url(true) . "&response_type=code"
                    . "&scope=snsapi_base&state={\"buffge\":\"这可以传递数据\"}#wechat_redirect";
                header("Location: {$url}");
                die;
            }
        } else {
            return Session::get('oauth2Info');
        }
    }

    /**
     *
     * @param string $accessToken
     * @param string $openId
     * @return array
     * @throws Exception
     */
    public static function getUserInfo(string $accessToken, string $openId): array
    {
        if (!Session::has('wxUserInfo')) {
            $res    = file_get_contents("https://api.weixin.qq.com/cgi-bin/user/info?access_token={$accessToken}&openid={$openId}&lang=zh_CN");
            $resArr = json_decode($res, true);
            if (is_null($resArr) || key_exists('errcode', $resArr)) {
                $errMsg = is_null($resArr) ? "微信接口返回的不是正确的json字符串" : $resArr['errmsg'];
                throw new Exception($errMsg);
            }
            Session::set('wxUserInfo', $resArr);
            return $resArr;
        } else {
            return Session::get('wxUserInfo');
        }
    }
}
