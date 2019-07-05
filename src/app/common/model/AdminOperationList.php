<?php
/**
 * Created by IntelliJ IDEA.
 * User: buff
 * Date: 2019/3/13
 * Time: 18:09
 */

namespace bdk\app\common\model;

/**
 * 管理员所拥有的操作权限
 * Class AdminOperationList
 * @package bdk\app\common\model
 */
class AdminOperationList
{
    protected $field = [
        'id', 'ctime', 'utime', 'dtime',
        'admin_id', 'operation_id',
    ];
}