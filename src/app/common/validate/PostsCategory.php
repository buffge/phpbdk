<?php
/**
 * Created by IntelliJ IDEA.
 * User: buff
 * Date: 2019/1/3
 * Time: 15:05
 */

namespace bdk\app\common\validate;


use bdk\app\common\model\PostsCategory as PostsCategoryModel;

class PostsCategory extends Base
{
    public const SCENE = [
        'add'  => 'add',
        'edit' => 'edit',
    ];
    protected $rule    = [
        'name'        => 'require|length:2,32|validNameExist:thinkphp',
        'pid'         => 'require|validPid:thinkphp',
        'picId'       => 'validPic:thinkphp',
        'description' => 'max:500',
        'editId'      => 'require|number',
        'editName'    => 'length:2,32|validNameExist:thinkphp',
    ];
    protected $message = [
        'name.require'        => '分类名必填',
        'name.length'         => '分类名长度为2-32位',
        'name.validNameExist' => '分类名已存在',
        'pid.require'         => '上级分类id必填',
        'pid.validPid'        => '上级分类id不正确',
        'picId.validPic'      => '图片不正确',
        'description.max'     => '描述最多500字',

        'editId.require'          => '分类id必填',
        'editId.number'           => '分类id必填',
        'editName.length'         => '分类名长度为2-32位',
        'editName.validNameExist' => '分类名已存在',

    ];
    protected $scene   = [
        self::SCENE['add']  => ['name', 'pid', 'picId', 'description',],
        self::SCENE['edit'] => ['editId', 'editName', 'picId', 'description',],
    ];

    /**
     * @param string $name
     * @param $rule
     * @param $data
     * @return bool
     */
    public function validNameExist(string $name, $rule, $data): bool
    {
        $map = [['name', '=', $name],];
        if ( array_key_exists('pid', $data) ) {
            $pid = $data['pid'];
            if ( array_key_exists('editId', $data) ) {
                $map[] = ['id', '<>', $data['editId']];
            }
        } else {
            $editId = $data['editId'];
            $pid    = PostsCategoryModel::getValue(['id' => $editId], 'pid');
            $map[]  = ['id', '<>', $editId];
        }
        $map[] = ['pid', '=', $pid];
        return PostsCategoryModel::getCount($map) === 0;
    }

    /**
     * 验证pid是否正确
     * @param  $pid
     * @param $rule
     * @param $data
     * @return bool
     */
    public function validPid($pid, $rule, $data): bool
    {
        return $pid === 0 || PostsCategoryModel::getCount([
                ['id', '=', $pid],
            ]) === 1;
    }

}