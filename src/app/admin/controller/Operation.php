<?php
/**
 * Created by IntelliJ IDEA.
 * User: buff
 * Date: 2019/1/3
 * Time: 13:42
 */

namespace bdk\app\admin\controller;

use bdk\app\common\controller\Base;
use bdk\app\common\model\{Log as BuffLog, Operation as OperationModel,};
use bdk\app\common\model\json\JsonResult;
use bdk\app\common\validate\Operation as OperationValid;
use bdk\app\http\middleware\AdminAuth;
use bdk\constant\JsonReturnCode;
use bdk\exception\NotFoundException;
use Exception;
use think\facade\Request;

class Operation extends Base
{
    protected $middleware = [
        AdminAuth::class => ['except' => ['']],
    ];


    /**
     * 添加操作
     */
    public function add(OperationValid $operationValid)
    {
        $validData = [
            'name'        => Request::post('name'),
            'action'      => Request::post('action'),
            'description' => Request::post('description'),
        ];
        if (!$operationValid->scene(OperationValid::SCENE['add'])->check($validData)) {
            return json([
                'code' => JsonReturnCode::VALID_ERROR,
                'msg'  => $operationValid->getError(),
            ]);
        }
        $json = new JsonResult;
        try {
            if (!OperationModel::addItem([
                'name'        => trim($validData['name']),
                'action'      => trim($validData['action']),
                'description' => trim($validData['description']),
                'cuid'        => $this->getUid(),
            ])) {
                $json->setCode(JsonReturnCode::TP_DB_ERROR);
                $json->setMsg('服务器错误,插入数据库失败');
            }
        } catch (Exception $ex) {
            BuffLog::sqlException($ex);
            $json->setCode(JsonReturnCode::TP_DB_ERROR);
            $json->setMsg($ex->getMessage());
        }
        return json($json);
    }

    /**
     * 删除操作
     */
    public function delete()
    {
        $operationIdArr = Request::has('id') ?
            [(int)Request::post('id')] :
            Request::post('idArr');
        $json           = ['code' => JsonReturnCode::SUCCESS,];
        try {
            if (!OperationModel::updateItem([
                ['id', 'in', $operationIdArr],
            ], [
                'dtime' => date('Y-m-d H:i:s'),
            ])) {
                $json['code'] = JsonReturnCode::DEFAULT_ERROR;
                $json['msg']  = '数据库删除失败';
            }
        } catch (Exception $ex) {
            $json['code'] = JsonReturnCode::SERVER_ERROR;
            $json['msg']  = $ex->getMessage();
            BuffLog::sqlException($ex);
        }
        return json($json);
    }

    /**
     * 修改操作
     */
    public function edit(OperationValid $operationValid)
    {
        $validData = [
            'editId'      => (int)Request::post('id'),
            'editName'    => trim(Request::post('name')),
            'editAction'  => trim(Request::post('action')),
            'description' => trim(Request::post('description')),
        ];
        if (!$operationValid->scene(OperationValid::SCENE['edit'])->check($validData)) {
            return json([
                'code' => JsonReturnCode::VALID_ERROR,
                'msg'  => $operationValid->getError(),
            ]);
        }
        $json = ['code' => JsonReturnCode::SUCCESS,];
        try {
            $operation         = OperationModel::get($validData['editId']);
            $operation->name   = $validData['editName'];
            $operation->action = $validData['editAction'];
            if (!empty($validData['description'])) {
                $operation->description = $validData['description'];
            }
            if (!$operation->save()) {
                $json['code'] = JsonReturnCode::SERVER_ERROR;
                $json['msg']  = "数据库更新失败";
            }
        } catch (Exception $ex) {
            $json['code'] = JsonReturnCode::SERVER_ERROR;
            $json['msg']  = $ex->getMessage();
            BuffLog::sqlException($ex);
        }
        return json($json);
    }

    /**
     * 查看操作详情
     */
    public function detail()
    {
        $json         = [
            'code' => JsonReturnCode::SUCCESS,
        ];
        $operationId  = (int)Request::get('id');
        $map          = [
            ['id', '=', $operationId],
            ['dtime', 'null', ''],
        ];
        $field        = [];
        $operation    = OperationModel::getDetail($map);
        $json['data'] = ['detail' => $operation,];
        return json($json);
    }

    /**
     * 查看操作列表
     */
    public function list()
    {
        $json  = [
            'code'  => JsonReturnCode::SUCCESS,
            'data'  => [
                'list' => [],
            ],
            'count' => 0,
        ];
        $page  = Request::has('page', 'get') ? (int)Request::get('page') : OperationModel::NOT_LIMIT;
        $limit = Request::has('limit', 'get') ? (int)Request::get('limit') : OperationModel::NOT_LIMIT;
        $map   = [
            ['dtime', 'null', ''],
        ];
        try {
            [$operationList, $count] = OperationModel::getList($page, $limit,
                OperationModel::NEED_COUNT, $map);
            $json['data']  = ['list' => $operationList];
            $json['count'] = $count;
        } catch (NotFoundException $ex) {
        } catch (Exception $ex) {
            $json['code'] = JsonReturnCode::SERVER_ERROR;
            $json['msg']  = $ex->getMessage();
            BuffLog::sqlException($ex);
        }
        return json($json);
    }


}