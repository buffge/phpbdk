<?php
/**
 * Created by IntelliJ IDEA.
 * User: buff
 * Date: 2018/12/29
 * Time: 17:52.
 */

namespace bdk\app\common\model;
/**
 * 推送相关信息
 * Class AppPush
 * @package bdk\app\common\model
 */
class AppPush extends Base
{
    protected $field = [
        'id', 'ctime', 'utime', 'dtime',
        'uid', 'os', 'device_id', 'type',

    ];
    protected $json  = [];
    public const OS   = [
        'android' => 0x0,
        'ios'     => 0x1,
    ];
    public const TYPE = [
        'ali' => 0x0,
    ];
}
