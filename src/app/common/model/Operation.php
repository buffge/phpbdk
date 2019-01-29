<?php
/**
 * Created by IntelliJ IDEA.
 * User: buff
 * Date: 2018/12/29
 * Time: 17:52.
 */

namespace bdk\app\common\model;

/**
 * 操作
 * Class Operation
 * @package app\common\model
 */
class Operation extends Base
{
    protected $field = [
        'id', 'ctime', 'utime', 'dtime',
        'name', 'action', 'description', 'cuid',
    ];
    protected $json  = [
    ];
}
