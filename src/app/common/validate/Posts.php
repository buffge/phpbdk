<?php
/**
 * Created by IntelliJ IDEA.
 * User: buff
 * Date: 2019/1/3
 * Time: 15:05
 */

namespace bdk\app\common\validate;

use bdk\app\common\model\Posts as PostsModel;
use bdk\app\common\model\PostsCategory as PostsCategoryModel;

class Posts extends Base
{
    public const SCENE = [
        'addText'    => 'addText',
        'addInt'     => 'addInt',
        'addFloat'   => 'addFloat',
        'addJson'    => 'addJson',
        'addPic'     => 'addPic',
        'addArticle' => 'addArticle',

        'editText'    => 'editText',
        'editInt'     => 'editInt',
        'editFloat'   => 'editFloat',
        'editJson'    => 'editJson',
        'editPic'     => 'editPic',
        'editArticle' => 'editArticle',
    ];
    protected $rule    = [
        'category'         => 'require|validCategory:thinkphp',
        'name'             => 'require|length:2,32|validNameExist:thinkphp',
        'textContent'      => 'require',
        'intContent'       => 'require|integer',
        'floatContent'     => 'require|float',
        'jsonContent'      => 'require|isJson:thinkphp',
        'picIdList'        => 'require|array|validPicIdList:thinkphp',
        'articlePicIdList' => 'array|validPicIdList:thinkphp',

        'articleContent' => 'require|length:0,100000',
        'title'          => 'require|length:1,64',
        'excerpt'        => 'max:500',
        'sort'           => 'integer|min:0',
        'sourceName'     => 'max:32',
        'sourceUrl'      => 'url',

        'editId' => 'require|integer',


    ];
    protected $message = [
        'category.require'       => '文章分类必填',
        'category.validCategory' => '文章分类不是正确的值',
        'type.require'           => '文章类型必填',
        'type.in'                => '文章类型不是正确的值',

        'name.require'        => '文章名必填',
        'name.length'         => '文章名长度为1-64位',
        'name.validNameExist' => '文章名已存在',

        'textContent.require' => '文章内容必填',

        'intContent.require' => '文章内容必填',
        'intContent.integer' => '文章内容必须为整数',

        'floatContent.require' => '文章内容必填',
        'floatContent.float'   => '文章内容必须为浮点数',

        'jsonContent.require' => '文章内容必填',
        'jsonContent.isJson'  => '文章内容必须为json格式',

        'picIdList.require'        => '图片必选',
        'picIdList.array'          => '图片列表格式不正确',
        'picIdList.validPicIdList' => '图片id不正确',

        'articleContent.require'          => '文章内容必填',
        'articleContent.length'           => '文章内容长度最多为10万字',
        'title.require'                   => '文章标题必填',
        'title.length'                    => '文章标题长度为1-64字',
        'excerpt.max'                     => '文章摘要最多500字',
        'sort.integer'                    => '文章排序必须为正整数',
        'sort.min'                        => '文章排序最小值为0',
        'sourceName.max'                  => '文章来源最多为32字',
        'sourceUrl.url'                   => '文章来源网址格式不正确',
        'articlePicIdList.array'          => '图片列表格式不正确',
        'articlePicIdList.validPicIdList' => '图片id不正确',

        'editId.require' => '文章id必须',
        'editId.integer' => '文章id必须为整数',

    ];
    protected $scene   = [
        self::SCENE['addText']    => ['category', 'type', 'name', 'textContent',],
        self::SCENE['addInt']     => ['category', 'type', 'name', 'intContent',],
        self::SCENE['addFloat']   => ['category', 'type', 'name', 'floatContent',],
        self::SCENE['addJson']    => ['category', 'type', 'name', 'jsonContent',],
        self::SCENE['addPic']     => ['category', 'type', 'name', 'picIdList',],
        self::SCENE['addArticle'] => [
            'category', 'type', 'title', 'articlePicIdList', 'articleContent',
            'excerpt', 'sort', 'sourceName', 'sourceUrl'],

        self::SCENE['editText']    => ['editId', 'category', 'type', 'name', 'textContent',],
        self::SCENE['editInt']     => ['editId', 'category', 'type', 'name', 'IntContent',],
        self::SCENE['editFloat']   => ['editId', 'category', 'type', 'name', 'FloatContent',],
        self::SCENE['editJson']    => ['editId', 'category', 'type', 'name', 'JsonContent',],
        self::SCENE['editPic']     => ['editId', 'category', 'type', 'name', 'picIdList',],
        self::SCENE['editArticle'] => [
            'editId', 'category', 'type', 'title', 'articlePicIdList', 'articleContent',
            'excerpt', 'sort', 'sourceName', 'sourceUrl',],
    ];

    public function __construct(array $rules = [], array $message = [], array $field = [])
    {
        parent::__construct($rules, $message, $field);
        $this->rule['type'] = 'require|in:' . implode(',', PostsModel::TYPE);
    }


    /**
     * @param string $name
     * @param $rule
     * @param $data
     * @return bool
     */
    public function validNameExist(string $name, $rule, $data): bool
    {
        $category = $data['category'];
        $map      = [
            ['category_id', '=', $category],
            ['name', '=', $name],
        ];
        if ( array_key_exists('editId', $data) ) {
            $map[] = ['id', '<>', $data['editId']];
        }
        return PostsModel::getCount($map) === 0;
    }

    /**
     * 验证category是否正确
     * @param  $category
     * @param $rule
     * @param $data
     * @return bool
     */
    public function validCategory($category, $rule, $data): bool
    {
        return $category === 0 || PostsCategoryModel::getCount([
                ['id', '=', $category],
            ]) === 1;
    }

}