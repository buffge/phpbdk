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
    protected $field = ['id', 'ctime', 'utime', 'dtime', 'uid',];
    protected $json  = [];

    /**
     * 管理员所拥有的权限
     * @return \think\model\relation\BelongsToMany
     */
    public function operations()
    {
        return $this->belongsToMany(Operation::class, AdminOperationList::class,
            'admin_id', 'operation_id');
    }

    /**
     * 管理员所拥有的权限组
     * @return \think\model\relation\BelongsToMany
     */
    public function operationGroups()
    {
        return $this->belongsToMany(OperationGroup::class, AdminOperationGroupList::class,
            'admin_id', 'operation_group_id');
    }
}
