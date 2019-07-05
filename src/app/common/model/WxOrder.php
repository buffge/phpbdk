<?php
/**
 * Created by IntelliJ IDEA.
 * User: buff
 * Date: 2018/12/29
 * Time: 17:52.
 */

namespace bdk\app\common\model;

class WxOrder extends Base
{
    /**
     * 微信通知状态
     */
    public const NOTIFY_STATUS    = [
        'waitPay'     => 0x0,
        'cancelPay'   => 0x1,
        'completePay' => 0x2,
    ];
    public const NOTIFY_STATUS_ZH = [
        self::NOTIFY_STATUS['waitPay']     => '等待支付',
        self::NOTIFY_STATUS['cancelPay']   => '取消支付',
        self::NOTIFY_STATUS['completePay'] => '完成支付',
    ];
    protected $field = [
        'id', 'ctime', 'utime', 'dtime',
        'appid', 'out_trade_no', 'transaction_id',
        'openid', 'body', 'detail', 'attach', 'money',
        'notify_status',

    ];
    protected $json  = [];

    /**
     * 订单是否已结束
     * @return bool
     */
    public function isFinish(): bool
    {
        return $this->notify_status !== self::NOTIFY_STATUS['waitPay'];
    }

}
