<?php
/**
 * Created by IntelliJ IDEA.
 * User: buff
 * Date: 2019/4/2
 * Time: 14:30
 */

namespace bdk\plug\wx;

use bdk\app\common\model\Log as BuffLog;
use bdk\app\common\model\UserWxInfo as UserWxInfoModel;
use bdk\app\common\model\WxApiRecord as WxApiRecordModel;
use EasyWeChat\Factory;
use Exception;
use think\facade\Config as TpConf;
use think\facade\Request;
use think\facade\Session;

class Common
{

    /**
     * @return \EasyWeChat\Payment\Application
     */
    public static function getWxPaymentApp(array $config = []): \EasyWeChat\Payment\Application
    {
        if ( empty($config) ) {
            $wxConf = TpConf::pull('wx');
            $config = [
                'app_id'     => $wxConf['appid'],
                'mch_id'     => $wxConf['mch_id'],
                'key'        => $wxConf['key'],
                'cert_path'  => $wxConf['public_pem'],
                'key_path'   => $wxConf['private_key'],
                'notify_url' => $wxConf['notify_url'],     // 你也可以在下单时单独设置来想覆盖它
            ];
        }
        return Factory::payment($config);
    }

    public static function getWxInfo()
    {
        return Session::get('oauth2Info');
    }

    /**
     * @return \EasyWeChat\OfficialAccount\Application
     */
    public static function getOfficialAccountApp(array $config = []):
    \EasyWeChat\OfficialAccount\Application
    {
        if ( empty($config) ) {
            $wxConf = TpConf::pull('wx');
            $config = [
                'app_id'        => $wxConf['appid'],
                'secret'        => $wxConf['secret'],
                'token'         => $wxConf['token'],
                'aes_key'       => $wxConf['aes_key'],
                'response_type' => 'array',
                'log'           => [
                    'level' => 'debug',
                    'file'  => __DIR__ . '/wechat.log',
                ],
                'response_type' => 'array',
                'oauth'         => [
                    'scopes'   => ['snsapi_userinfo'],
                    'callback' => Request::url(true),
                ],
            ];
        }
        return Factory::officialAccount($config);
    }

    /**
     * 统一下单
     * @param int $uid
     * @param string $body
     * @param string $outTradeNo
     * @param int $totalFee
     * @param string $tradeType
     * @param string $openid
     * @return array
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public static function unifiedOrder(int $uid, string $body, string $outTradeNo, int $totalFee,
                                        string $openid, $tradeType = 'JSAPI'): array
    {
        $wxConf = TpConf::pull('wx');
        $app    = self::getWxPaymentApp();
        $result = $app->order->unify([
            'body'         => $body,
            'out_trade_no' => $outTradeNo,
            'total_fee'    => $totalFee,
            'trade_type'   => $tradeType,
            'openid'       => $openid,
        ]);
        if ( !WxApiRecordModel::addItem([
            'appid'       => $wxConf['appid'],
            'uid'         => $uid,
            'type'        => WxApiRecordModel::TYPE['unifiedOrder'],
            'unique_flag' => $outTradeNo,
            'msg'         => '统一下单',
            'extra'       => json_encode([
                'result' => $result,
                'get'    => Request::get(),
                'post'   => Request::post(),
                'ip'     => Request::ip(),
            ], JSON_UNESCAPED_UNICODE),
        ]) ) {
            throw new Exception('添加微信api记录失败');
        }
        return $result;

    }

    /**
     * 判断是否为微信客户端
     * @return bool
     */
    public static function isWxClient(): bool
    {
        return key_exists('HTTP_USER_AGENT', $_SERVER) && strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false;
    }

    /**
     * 发送模板消息
     * @param int $uid
     * @param string $tplId
     * @param string $url
     * @return array
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     */
    public static function sendTplMsg(int $uid, string $tplId, string $url, array $data): array
    {
        $wxConf = TpConf::pull('wx');
        $app    = self::getOfficialAccountApp();
        $openid = UserWxInfoModel::getValue(['uid' => $uid,], 'openid');
        $data   = [
            'touser'      => $openid,
            'template_id' => $tplId,
            'url'         => $url,
            'data'        => $data,
        ];
        $res    = $app->template_message->send($data);
        if ( WxApiRecordModel::addItem([
            'appid'       => $wxConf['appid'],
            'uid'         => $uid,
            'type'        => WxApiRecordModel::TYPE['tplMsg'],
            'msg'         => '发送模板消息',
            'extra'       => [
                'data' => $data,
                'res'  => $res,
            ],
            'unique_flag' => $res['msgid'],
        ]) ) {
            BuffLog::debug([
                'data' => $data,
                'ip'   => Request::ip(),
                'get'  => Request::get(),
                'post' => Request::post(),
            ], '新增微信api记录失败');
        }
        if ( $res['errcode'] !== 0 || $res['errmsg'] !== 'ok' ) {
            return [$res, false];
        }
        return [$res, true];
    }
}