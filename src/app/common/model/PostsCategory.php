<?php
/**
 * Created by IntelliJ IDEA.
 * User: buff
 * Date: 2018/12/29
 * Time: 17:52.
 */

namespace bdk\app\common\model;
/**
 * 文章分类
 * Class PostsCategory
 * @package bdk\app\common\model
 */
class PostsCategory extends Base
{
    protected $field = [
        'id', 'ctime', 'utime', 'dtime',
        'name', 'pid', 'description', 'pic_id',

    ];
    protected $json  = [];

    /**
     * 图片
     * @return \think\model\relation\HasOne
     */
    public function picture()
    {
        return $this->hasOne(Picture::class, 'id', 'pic_id');
    }

    /**
     * 构建子分类
     */
    public function buildChildren()
    {
        $map               = [
            'pid' => $this->id,
        ];
        $field             = [];
        $childCategoryList = static::getListNotThrowEmptyEx(self::NOT_LIMIT, self::NOT_LIMIT,
            self::NOT_NEED_COUNT, $map, $field);
        foreach ($childCategoryList as $childCategory) {
            if ( $childCategory->picture ) {
                $childCategory->picture->visible(['id', 'title', 'url']);
            }
            $childCategory->children = $childCategory->buildChildren();
            $childCategory->visible([
                'id', 'name', 'pid', 'description',]);
        }
        return $childCategoryList;
    }
}
