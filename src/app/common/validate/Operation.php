<?php
/**
 * Created by IntelliJ IDEA.
 * User: buff
 * Date: 2019/1/3
 * Time: 15:05
 */

namespace bdk\app\common\validate;


use bdk\app\common\model\Operation as OperationModel;

class Operation extends Base
{
    public const SCENE = [
        'add'  => 'add',
        'edit' => 'edit',
    ];
    protected $rule    = [
        'name'        => 'require|length:2,32|unique:operation,name',
        'action'      => 'require|unique:operation,action|max:128',
        'editId'      => 'require|number',
        'editName'    => 'require|length:2,32|validNameExist:thinkphp',
        'editAction'  => 'require|max:128|validActionNameExist:thinkphp',
        'description' => 'max:300',
    ];
    protected $message = [
        'name.require'   => '操作名必填',
        'name.length'    => '操作名长度为2-32位',
        'name.unique'    => '操作名已存在',
        'action.require' => '方法名必填',
        'action.length'  => '方法名已存在',
        'action.max'     => '方法名长度最大为128位',

        'editId.require'                  => '操作id必填',
        'editId.number'                   => '操作id必填',
        'editName.require'                => '操作名必填',
        'editName.length'                 => '操作名长度为2-32位',
        'editName.validNameExist'         => '操作名已存在',
        'editAction.require'              => '操作方法必填',
        'editAction.length'               => '操作方法长度最大为128位',
        'editAction.validActionNameExist' => '操作方法名已存在',

        'description.max' => '描述最多300字',
    ];
    protected $scene   = [
        self::SCENE['add']  => ['name', 'action', 'description',],
        self::SCENE['edit'] => ['editId', 'editName', 'editAction', 'description',],
    ];

    /**
     * @param string $name
     * @param $rule
     * @param $data
     * @return bool
     */
    public function validNameExist(string $name, $rule, $data): bool
    {
        $operationId = $data['editId'];
        return OperationModel::getCount([
                ['id', '<>', $operationId],
                ['name', '=', $name],
            ]) === 0;
    }

    /**
     * @param string $action
     * @param $rule
     * @param $data
     * @return bool
     */
    public function validActionNameExist(string $action, $rule, $data): bool
    {
        $operationId = $data['editId'];
        return OperationModel::getCount([
                ['id', '<>', $operationId],
                ['action', '=', $action],
            ]) === 0;
    }
}