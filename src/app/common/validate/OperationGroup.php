<?php

/*
 * Author: buff <admin@buffge.com>
 * Created on : 2018-8-1, 11:27:56
 * QQ:1515888956
 */

namespace bdk\app\common\validate;
use bdk\app\common\model\OperationGroup as OperationGroupModel;
/**
 * 操作组
 */
class OperationGroup extends Base
{
    public const SCENE = [
        'add'  => 'add',
        'edit' => 'edit',
    ];
    protected $rule    = [
        'id'                   => 'require|integer',
        'name'                 => 'require|length:1,10|chsDash|unique:operation_group',
        'editName'             => 'require|length:1,10|chsDash|validEditNameExist:thinkphp',
        'operation_list'       => 'require|array',
        'operation_group_list' => 'array',
        'description'          => 'length:1,500',
    ];
    protected $message = [
        'id.require'   => '操作组id必填',
        'id.integer'   => '操作组id必须为整数',
        'name.require' => '操作组名必填',
        'name.length'  => '操作组名长度为1-10',
        'name.chsDash' => '操作组名必须是汉字字母数字下划线和破折号',
        'name.unique'  => '操作组名已存在',

        'editName.require'       => '操作组名必填',
        'editName.length'        => '操作组名长度为1-10',
        'editName.chsDash'       => '操作组名必须是汉字字母数字下划线和破折号',
        'editName.validEditName' => '操作组名已存在',

        'operation_list.require'     => '操作列表必填',
        'operation_list.array'       => '操作列表不是正确的数组格式',
        'operation_group_list.array' => '操作组列表不是正确的数组格式',
        'description.length'         => '操作组描述长度为1-500',
    ];
    protected $scene   = [
        self::SCENE['add']  => ['name', 'operation_list', 'operation_group_list', 'description',],
        self::SCENE['edit'] => ['id', 'editName', 'operation_list', 'operation_group_list', 'description',],
    ];

    /**
     * @param string $name
     * @param $rule
     * @param $data
     * @return bool
     */
    public function validEditNameExist(string $name, $rule, $data): bool
    {
        $operationId = $data['id'];
        return OperationGroupModel::getCount([
                ['id', '<>', $operationId],
                ['name', '=', $name],
            ]) === 0;
    }
}
