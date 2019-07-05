<?php
/**
 * Created by IntelliJ IDEA.
 * User: buff
 * Date: 2019/1/3
 * Time: 14:02
 */

namespace bdk\app\admin\controller;

use bdk\app\common\controller\Base;
use bdk\app\common\model\Log as BuffLog;
use bdk\app\common\model\PostsCategory as PostsCategoryModel;
use bdk\app\common\validate\PostsCategory as PostsCategoryValid;
use bdk\app\http\middleware\AdminAuth;
use bdk\constant\JsonReturnCode;
use Exception;
use think\facade\Request;

/**
 * 文章分类
 * Class PostsCategory
 * @package bdk\app\admin\controller
 */
class PostsCategory extends Base
{
    protected $middleware = [
        AdminAuth::class => ['except' => []],
    ];

    /**
     * 添加分类
     * @param PostsCategoryValid $postsCategoryValid
     * @route /admin/postsCategory/add post
     * @return \think\response\Json
     */
    public function add(PostsCategoryValid $postsCategoryValid): \think\response\Json
    {
        $json      = ['code' => JsonReturnCode::SUCCESS];
        $validData = [
            'name' => trim(Request::post('name')),
            'pid'  => (int)Request::post('pid'),
        ];
        if ( Request::has('picId', 'post') ) {
            $validData['picId'] = (int)Request::post('picId');
        }
        if ( Request::has('description', 'post') ) {
            $validData['description'] = trim(Request::post('description'));
        }
        if ( !$postsCategoryValid->scene(PostsCategoryValid::SCENE['add'])->check($validData) ) {
            return json([
                'code' => JsonReturnCode::VALID_ERROR,
                'msg'  => $postsCategoryValid->getError(),
            ]);
        }
        try {
            $addData = [
                'pid'  => $validData['pid'],
                'name' => $validData['name'],
            ];
            if ( array_key_exists('picId', $validData) ) {
                $addData['pic_id'] = $validData['picId'];
            }
            if ( array_key_exists('description', $validData) ) {
                $addData['description'] = $validData['description'];
            }
            if ( !PostsCategoryModel::addItem($addData) ) {
                return json([
                    'code' => JsonReturnCode::DEFAULT_ERROR,
                    'msg'  => '添加分类失败',
                ]);
            }
        } catch (Exception $ex) {
            BuffLog::sqlException($ex);
            $json['code'] = JsonReturnCode::SERVER_ERROR;
            $json['msg']  = $ex->getMessage();
        }
        return json($json);
    }

    /**
     * 编辑分类
     * @param PostsCategoryValid $postsCategoryValid
     * @route /admin/postsCategory/edit post
     * @return \think\response\Json
     */
    public function edit(PostsCategoryValid $postsCategoryValid): \think\response\Json
    {
        $json      = ['code' => JsonReturnCode::SUCCESS];
        $validData = [
            'editId'   => trim(Request::post('id')),
            'editName' => trim(Request::post('name')),
        ];
        if ( Request::has('picId', 'post') ) {
            $validData['picId'] = (int)Request::post('picId');
        }
        if ( Request::has('description', 'post') ) {
            $validData['description'] = trim(Request::post('description'));
        }
        if ( Request::has('pid', 'post') ) {
            $validData['pid'] = (int)Request::post('pid');
        }
        if ( !$postsCategoryValid->scene(PostsCategoryValid::SCENE['edit'])->check($validData) ) {
            return json([
                'code' => JsonReturnCode::VALID_ERROR,
                'msg'  => $postsCategoryValid->getError(),
            ]);
        }
        try {
            $editData = [
                'name' => $validData['editName'],
            ];
            if ( array_key_exists('picId', $validData) ) {
                $editData['pic_id'] = $validData['picId'];
            }
            if ( array_key_exists('description', $validData) ) {
                $editData['description'] = $validData['description'];
            }
            if ( !PostsCategoryModel::updateItem([
                'id' => $validData['editId'],
            ], $editData) ) {
                return json([
                    'code' => JsonReturnCode::DEFAULT_ERROR,
                    'msg'  => '编辑分类失败',
                ]);
            }
        } catch (Exception $ex) {
            BuffLog::sqlException($ex);
            $json['code'] = JsonReturnCode::SERVER_ERROR;
            $json['msg']  = $ex->getMessage();
        }
        return json($json);
    }

    /**
     * 删除分类
     * @route /admin/postsCategory/delete post
     * @return \think\response\Json
     */
    public function delete(): \think\response\Json
    {
        $json = ['code' => JsonReturnCode::SUCCESS];
        $ids  = Request::post('ids');
        try {
            if ( !PostsCategoryModel::updateItem([
                ['id', 'in', $ids],
            ], [
                'dtime' => date('Y-m-d H:i:s'),
            ]) ) {
                return json([
                    'code' => JsonReturnCode::SERVER_ERROR,
                    'msg'  => '删除失败',
                ]);
            }
        } catch (Exception $ex) {
            BuffLog::sqlException($ex);
            $json['code'] = JsonReturnCode::SERVER_ERROR;
            $json['msg']  = $ex->getMessage();
        }
        return json($json);
    }

    /**
     * 获取列表
     * @route /listPrescriptionTpl get
     * @return \think\response\Json
     */
    public function list(): \think\response\Json
    {
        $json                  = ['code' => JsonReturnCode::SUCCESS];
        $page                  = (int)Request::get('page');
        $limit                 = (int)Request::get('limit');
        $filter                = Request::get('filter');
        $filterFieldMapDbField = [
            'id' => 'id',
        ];
        try {
            $map = [
                ['pid', '=', PostsCategoryModel::NOT_HAVE_PARENT],
            ];
            $map = array_merge($map, PostsCategoryModel::buildWhereMap($filter, $filterFieldMapDbField));
            [$categoryList, $count] = PostsCategoryModel::getListNotThrowEmptyEx($page,
                $limit,
                PostsCategoryModel::NEED_COUNT, $map);
            $resList = [];
            foreach ($categoryList as $category) {
                if ( $category->picture ) {
                    $category->picture->visible(['id', 'title', 'url']);
                }
                $category->children = $category->buildChildren();
                $category->visible([
                    'id', 'name', 'pid', 'description',]);
                $resList[] = $category;
            }
            $json['data']  = [
                'list' => $resList,
            ];
            $json['count'] = $count;
        } catch (Exception $ex) {
            BuffLog::sqlException($ex);
            $json['code'] = JsonReturnCode::SERVER_ERROR;
            $json['msg']  = $ex->getMessage();
        }
        return json($json);
    }

}