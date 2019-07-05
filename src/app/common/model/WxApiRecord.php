<?php
/**
 * Created by IntelliJ IDEA.
 * User: buff
 * Date: 2019/3/29
 * Time: 14:48
 */

namespace bdk\app\common\model;

/**
 * 微信api记录
 * Class WxApiRecord
 * @package bdk\app\common\model
 */
class WxApiRecord extends Base
{
    public const TYPE    = [
        'unifiedOrder' => 0x0,
        'refund'       => 0x1,
        'tplMsg'       => 0x2,
    ];
    public const TYPE_ZH = [
        self::TYPE['unifiedOrder'] => '统一下单',
        self::TYPE['refund']       => '退款',
        self::TYPE['tplMsg']       => '模板消息',
    ];
    protected $field = [
        'id', 'ctime', 'utime', 'dtime',
        'appid', 'uid', 'type', 'msg', 'extra', 'unique_flag',
    ];
    protected $json  = [
        'extra',
    ];
}