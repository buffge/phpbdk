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
use bdk\app\common\model\Posts as PostsModel;
use bdk\app\common\validate\Posts as PostsValid;
use bdk\app\http\middleware\AdminAuth;
use bdk\constant\JsonReturnCode;
use Exception;
use think\facade\Request;

/**
 * 文章
 * Class Posts
 * @package bdk\app\admin\controller
 */
class Posts extends Base
{
    protected $middleware = [
        AdminAuth::class => ['except' => []],
    ];

    /**
     * 添加文章
     * @param PostsValid $postsValid
     * @route /admin/posts/add post
     * @return \think\response\Json
     */
    public function add(PostsValid $postsValid): \think\response\Json
    {
        $uid       = $this->getUid();
        $json      = ['code' => JsonReturnCode::SUCCESS];
        $postsType = Request::post('type');
        if ( !in_array($postsType, PostsModel::TYPE) ) {
            return json([
                'code' => JsonReturnCode::INVAILD_PARAM,
                'msg'  => '缺少type字段',
            ]);
        }
        $validData = [
            'category' => Request::post('category'),
            'type'     => $postsType,
        ];
        $addData   = [
            'author_uid'  => $uid,
            'category_id' => $validData['category'],
            'type'        => $validData['type'],
        ];
        $scene;
        switch ($postsType) {
            case PostsModel::TYPE['text']:
                $validData['name']        = trim(Request::post('name'));
                $validData['textContent'] = trim(Request::post('content'));
                $addData['name']          = $validData['name'];
                $addData['content']       = $validData['textContent'];
                $scene                    = PostsValid::SCENE['addText'];
                break;
            case PostsModel::TYPE['int']:
                $validData['name']       = trim(Request::post('name'));
                $validData['intContent'] = Request::post('content');
                $addData['name']         = $validData['name'];
                $addData['content']      = $validData['intContent'];
                $scene                   = PostsValid::SCENE['addInt'];
                break;
            case PostsModel::TYPE['double']:
                $validData['name']         = trim(Request::post('name'));
                $validData['floatContent'] = Request::post('content');
                $addData['name']           = $validData['name'];
                $addData['content']        = $validData['floatContent'];
                $scene                     = PostsValid::SCENE['addFloat'];
                break;
            case PostsModel::TYPE['json']:
                $validData['name']        = trim(Request::post('name'));
                $validData['jsonContent'] = Request::post('content');
                $addData['name']          = $validData['name'];
                $addData['content']       = $validData['jsonContent'];
                $scene                    = PostsValid::SCENE['addJson'];
                break;
            case PostsModel::TYPE['pic']:
                $validData['name']      = trim(Request::post('name'));
                $validData['picIdList'] = Request::post('picIdList');
                $addData['name']        = $validData['name'];
                $addData['pic_id_list'] = $validData['picIdList'];
                $scene                  = PostsValid::SCENE['addPic'];
                break;
            case PostsModel::TYPE['article']:
                $validData['title']            = trim(Request::post('title'));
                $validData['excerpt']          = trim(Request::post('excerpt'));
                $validData['sort']             = Request::post('sort') ?? 0;
                $validData['sourceName']       = trim(Request::post('sourceName'));
                $validData['sourceUrl']        = trim(Request::post('sourceUrl'));
                $validData['articlePicIdList'] = Request::post('picIdList');
                $validData['articleContent']   = trim(Request::post('content'));
                $addData['name']               = $validData['title'];
                $addData['title']              = $validData['title'];
                $addData['excerpt']            = $validData['excerpt'];
                $addData['sort']               = $validData['sort'];
                $addData['source_name']        = $validData['sourceName'];
                $addData['source_url']         = $validData['sourceUrl'];
                $addData['content']            = $validData['articleContent'];
                $addData['pic_id_list']        = $validData['articlePicIdList'];
                $scene                         = PostsValid::SCENE['addArticle'];
                break;
            default:
                break;
        }
        try {
            if ( !$postsValid->scene($scene)->check($validData) ) {
                return json([
                    'code' => JsonReturnCode::VALID_ERROR,
                    'msg'  => $postsValid->getError(),
                ]);
            }
            if ( !PostsModel::addItem($addData) ) {
                return json([
                    'code' => JsonReturnCode::DEFAULT_ERROR,
                    'msg'  => '添加文章失败',
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
     * 编辑文章
     * @param PostsValid $postsValid
     * @route /admin/posts/edit post
     * @return \think\response\Json
     */
    public function edit(PostsValid $postsValid): \think\response\Json
    {
        $uid       = $this->getUid();
        $json      = ['code' => JsonReturnCode::SUCCESS];
        $postsType = Request::post('type');
        if ( !in_array($postsType, PostsModel::TYPE) ) {
            return json([
                'code' => JsonReturnCode::INVAILD_PARAM,
                'msg'  => '缺少type字段',
            ]);
        }
        $validData  = [
            'editId'   => Request::post('id'),
            'category' => Request::post('category'),
            'type'     => $postsType,
        ];
        $updateData = [
            'author_uid'  => $uid,
            'category_id' => $validData['category'],
            'type'        => $validData['type'],
        ];
        $scene;
        switch ($postsType) {
            case PostsModel::TYPE['text']:
                $validData['name']        = trim(Request::post('name'));
                $validData['textContent'] = trim(Request::post('content'));
                $updateData['name']       = $validData['name'];
                $updateData['content']    = $validData['textContent'];
                $scene                    = PostsValid::SCENE['editText'];
                break;
            case PostsModel::TYPE['int']:
                $validData['name']       = trim(Request::post('name'));
                $validData['intContent'] = Request::post('content');
                $updateData['name']      = $validData['name'];
                $updateData['content']   = $validData['intContent'];
                $scene                   = PostsValid::SCENE['editInt'];
                break;
            case PostsModel::TYPE['double']:
                $validData['name']         = trim(Request::post('name'));
                $validData['floatContent'] = Request::post('content');
                $updateData['name']        = $validData['name'];
                $updateData['content']     = $validData['floatContent'];
                $scene                     = PostsValid::SCENE['editFloat'];
                break;
            case PostsModel::TYPE['json']:
                $validData['name']        = trim(Request::post('name'));
                $validData['jsonContent'] = Request::post('content');
                $updateData['name']       = $validData['name'];
                $updateData['content']    = $validData['jsonContent'];
                $scene                    = PostsValid::SCENE['editJson'];
                break;
            case PostsModel::TYPE['pic']:
                $validData['name']         = trim(Request::post('name'));
                $validData['picIdList']    = Request::post('picIdList');
                $updateData['name']        = $validData['name'];
                $updateData['pic_id_list'] = $validData['picIdList'];
                $updateData['content']     = '';
                $scene                     = PostsValid::SCENE['editPic'];
                break;
            case PostsModel::TYPE['article']:
                $validData['title']            = trim(Request::post('title'));
                $validData['excerpt']          = trim(Request::post('excerpt'));
                $validData['sort']             = Request::post('sort') ?? 0;
                $validData['sourceName']       = trim(Request::post('sourceName'));
                $validData['sourceUrl']        = trim(Request::post('sourceUrl'));
                $validData['articlePicIdList'] = Request::post('picIdList');
                $validData['articleContent']   = trim(Request::post('content'));
                $updateData['name']            = $validData['title'];
                $updateData['title']           = $validData['title'];
                $updateData['excerpt']         = $validData['excerpt'];
                $updateData['sort']            = $validData['sort'];
                $updateData['source_name']     = $validData['sourceName'];
                $updateData['source_url']      = $validData['sourceUrl'];
                $updateData['content']         = $validData['articleContent'];
                $updateData['pic_id_list']     = $validData['articlePicIdList'];
                $scene                         = PostsValid::SCENE['editArticle'];
                break;
            default:
                break;
        }
        if ( in_array($postsType, [
            PostsModel::TYPE['text'], PostsModel::TYPE['int'],
            PostsModel::TYPE['double'], PostsModel::TYPE['json'],
            PostsModel::TYPE['pic'],
        ]) ) {
            $updateData['title']       = '';
            $updateData['excerpt']     = '';
            $updateData['sort']        = 0;
            $updateData['source_name'] = '';
            $updateData['source_url']  = '';
            $updateData['view_count']  = 0;
        }
        if ( $postsType !== PostsModel::TYPE['pic'] && $postsType !== PostsModel::TYPE['article']
        ) {
            $updateData['pic_id_list'] = null;
        }
        try {
            if ( !$postsValid->scene($scene)->check($validData) ) {
                return json([
                    'code' => JsonReturnCode::VALID_ERROR,
                    'msg'  => $postsValid->getError(),
                ]);
            }

            if ( !PostsModel::updateItem([
                'id' => $validData['editId'],
            ], $updateData) ) {
                return json([
                    'code' => JsonReturnCode::DEFAULT_ERROR,
                    'msg'  => '无更新',
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
     * 删除文章
     * @route /admin/posts/delete post
     * @return \think\response\Json
     */
    public function delete(): \think\response\Json
    {
        $json = ['code' => JsonReturnCode::SUCCESS];
        $ids  = Request::post('ids');
        try {
            if ( !PostsModel::updateItem([
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
     * 获取文章列表
     * @route /admin/posts/list get
     * @return \think\response\Json
     */
    public function list(): \think\response\Json
    {
        $json                  = ['code' => JsonReturnCode::SUCCESS];
        $page                  = (int)Request::get('page');
        $limit                 = (int)Request::get('limit');
        $filter                = Request::get('filter');
        $filterFieldMapDbField = [
            'id'    => 'id',
            'name'  => 'name',
            'title' => 'title',
        ];
        try {
            $map = [];
            $map = array_merge($map, PostsModel::buildWhereMap($filter, $filterFieldMapDbField));
            [$dataList, $count] = PostsModel::getListNotThrowEmptyEx($page,
                $limit,
                PostsModel::NEED_COUNT, $map);
            $resList = [];
            foreach ($dataList as $data) {
                if ( $data->author->avatar ) {
                    $data->author->avatar->visible(['id', 'title', 'url']);
                }
                $data->author->visible(['id', 'name', 'nick',]);
                $data->mimeType   = $data->mime_type;
                $data->viewCount  = $data->view_count;
                $data->sourceName = $data->source_name;
                $data->sourceUrl  = $data->source_url;
                $data->picList    = $data->pictures();
                if ( $data->category ) {
                    if ( $data->category->picture ) {
                        $data->category->picture->visible(['id', 'title', 'url']);
                    }
                    $data->category->visible(['id', 'name', 'pid', 'description']);
                }
                $data->visible([
                    'id', 'name', 'content', 'title', 'excerpt', 'type', 'sort', 'status',
                    'ctime', 'utime', 'sourceName', 'sourceUrl']);
                $resList[] = $data;
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

    /**
     * 获取文章
     * @route /admin/posts/detail get
     * @return \think\response\Json
     */
    public function detail(): \think\response\Json
    {
        $json    = ['code' => JsonReturnCode::SUCCESS];
        $postsId = Request::get('id');
        try {
            $data = PostsModel::get($postsId);
            if ( $data->author->avatar ) {
                $data->author->avatar->visible(['id', 'title', 'url']);
            }
            $data->author->visible(['id', 'name', 'nick',]);
            $data->mimeType   = $data->mime_type;
            $data->viewCount  = $data->view_count;
            $data->sourceName = $data->source_name;
            $data->sourceUrl  = $data->source_url;
            if ( $data->category ) {
                if ( $data->category->picture ) {
                    $data->category->picture->visible(['id', 'title', 'url']);
                }
                $data->category->visible(['id', 'name', 'pid', 'description']);
            }
            $data->picList = $data->pictures();
            $data->visible([
                'id', 'name', 'content', 'title', 'excerpt', 'type', 'sort', 'status',
                'ctime', 'utime', 'sourceName', 'sourceUrl']);
            $json['data'] = [
                'detail' => $data,
            ];
        } catch (Exception $ex) {
            BuffLog::sqlException($ex);
            $json['code'] = JsonReturnCode::SERVER_ERROR;
            $json['msg']  = $ex->getMessage();
        }
        return json($json);
    }

}