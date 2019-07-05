<?php
/**
 * Created by IntelliJ IDEA.
 * User: buff
 * Date: 2019/3/13
 * Time: 18:09
 */

namespace bdk\app\common\model;

/**
 * 管理员所拥有的操作组权限
 * Class AdminOperationGroupList
 * @package bdk\app\common\model
 */
class AdminOperationGroupList
{
    protected $field = [
        'id', 'ctime', 'utime', 'dtime',
        'admin_id', 'operation_group_id',
    ];
}