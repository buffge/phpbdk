<?php
/**
 * Created by IntelliJ IDEA.
 * User: buff
 * Date: 2018/12/29
 * Time: 17:52.
 */

namespace bdk\app\common\model;
/**
 * app 在前台还是后台
 * Class AppStateStatus
 * @package bdk\app\common\model
 */
class AppStateStatus extends Base
{
    public const STATUS = [
        'active'     => 0x0,
        'background' => 0x1,
    ];
    protected $field = [
        'id', 'ctime', 'utime', 'dtime',
        'uid', 'status',

    ];
    protected $json  = [];
}
