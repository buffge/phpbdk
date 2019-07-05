<?php
/**
 * Created by IntelliJ IDEA.
 * User: buff
 * Date: 2018/12/29
 * Time: 17:52.
 */

namespace bdk\app\common\model;
/**
 * 微信退款订单
 * Class WxRefundOrder
 * @package bdk\app\common\model
 */
class WxRefundOrder extends Base
{
    /**
     * 微信通知状态
     */
    public const NOTIFY_STATUS    = [
        'refunding'      => 0x0,
        'completeRefund' => 0x1,
    ];
    public const NOTIFY_STATUS_ZH = [
        self::NOTIFY_STATUS['refunding']      => '退款中',
        self::NOTIFY_STATUS['completeRefund'] => '完成退款',
    ];
    protected $field = [
        'id', 'ctime', 'utime', 'dtime',
        'appid', 'out_refund_no', 'total_fee', 'refund_fee',
        'refund_desc', 'wx_order_id',
        'refund_account', 'wx_refund_id', 'refund_recv_account',
        'refund_request_source', 'success_time', 'transaction_id',
        'notify_status',
    ];
    protected $json  = [];
}
