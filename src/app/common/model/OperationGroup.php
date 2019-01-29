<?php
/**
 * Created by IntelliJ IDEA.
 * User: buff
 * Date: 2018/12/29
 * Time: 17:52.
 */

namespace bdk\app\common\model;

/**
 * 操作组
 * Class Operation
 * @package app\common\model
 */
class OperationGroup extends Base
{
    protected $field = [
        'id', 'ctime', 'utime', 'dtime',
        'name', 'description', 'operation_list',
        'operation_group_list', 'cuid',
    ];
    protected $json  = [
        'operation_list', 'operation_group_list',
    ];
}
