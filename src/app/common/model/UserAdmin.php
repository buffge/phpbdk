<?php
/**
 * Created by IntelliJ IDEA.
 * User: buff
 * Date: 2018/12/29
 * Time: 17:52.
 */

namespace bdk\app\common\model;

/**
 * 管理员信息
 * Class UserAdmin
 * @package bdk\app\common\model
 */
class UserAdmin extends Base
{
    protected $field = [
        'id', 'ctime', 'utime', 'dtime',
        'uid',
        'operation_list', 'operation_group_list',
    ];
    protected $json  = [
        'operation_list', 'operation_group_list',
    ];

}
