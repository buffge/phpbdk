<?php
/**
 * Created by IntelliJ IDEA.
 * User: buff
 * Date: 2019/1/3
 * Time: 13:42
 */

namespace bdk\app\admin\controller;

use bdk\app\common\controller\Base;
use bdk\app\common\model\{Log as BuffLog, Operation as OperationModel, OperationGroup as OperationGroupModel,};
use bdk\app\common\model\json\JsonResult;
use bdk\app\common\validate\OperationGroup as OperationGroupValid;
use bdk\constant\JsonReturnCode;
use bdk\exception\NotFoundException;
use Exception;
use think\facade\Request;

class OperationGroup extends Base
{
    /**
     * 添加操作组
     */
    public function add(OperationGroupValid $operationGroupValid)
    {
        $validData = [
            'name'                 => Request::post('name'),
            'operation_list'       => Request::post('operationList'),
            'operation_group_list' => Request::post('operationGroupList'),
            'description'          => Request::post('description'),
        ];
        if (!$operationGroupValid->scene(OperationGroupValid::SCENE['add'])->check($validData)) {
            return json([
                'code' => JsonReturnCode::VALID_ERROR,
                'msg'  => $operationGroupValid->getError(),
            ]);
        }
        $json = new JsonResult;
        try {
            if (!OperationGroupModel::addItem([
                'name'                 => trim($validData['name']),
                'operation_list'       => $validData['operation_list'],
                'operation_group_list' => $validData['operation_group_list'],
                'description'          => trim($validData['description']),
                'cuid'                 => $this->getUid(),
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
     * 删除操作组
     */
    public function delete()
    {
        $operationGroupIdArr = Request::has('id') ?
            [(int)Request::post('id')] :
            Request::post('idArr');
        $json                = ['code' => JsonReturnCode::SUCCESS,];
        try {
            if (!OperationGroupModel::updateItem([
                ['id', 'in', $operationGroupIdArr],
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
     * 修改操作组
     */
    public function edit(OperationGroupValid $operationGroupValid)
    {
        $validData = [
            'id'                   => (int)Request::post('id'),
            'editName'             => trim(Request::post('name')),
            'operation_list'       => Request::post('operationList'),
            'operation_group_list' => Request::post('operationGroupList'),
            'description'          => Request::post('description'),
        ];
        if (!$operationGroupValid->scene(OperationGroupValid::SCENE['edit'])->check($validData)) {
            return json([
                'code' => JsonReturnCode::VALID_ERROR,
                'msg'  => $operationGroupValid->getError(),
            ]);
        }
        $json = ['code' => JsonReturnCode::SUCCESS,];
        try {
            $operationGroup                       = OperationGroupModel::get($validData['id']);
            $operationGroup->name                 = $validData['editName'];
            $operationGroup->operation_list       = $validData['operation_list'];
            $operationGroup->operation_group_list = $validData['operation_group_list'];
            if (!empty($validData['description'])) {
                $operationGroup->description = $validData['description'];
            }
            if (!$operationGroup->save()) {
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
     * 查看操作组详情
     */
    public function detail()
    {
        $json             = [
            'code' => JsonReturnCode::SUCCESS,
        ];
        $operationGroupId = (int)Request::get('id');
        $operationGroup   = OperationGroupModel::get($operationGroupId);
        if ($operationGroup->isDel()) {
            $json['code'] = JsonReturnCode::INVAILD_PARAM;
            $json['msg']  = '操作组不存在';
        } else {
            $json['data'] = ['detail' => $operationGroup,];
        }
        return json($json);
    }

    /**
     * 查看操作组列表
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
            [$operationGroupList, $count] = OperationGroupModel::getList($page, $limit,
                OperationGroupModel::NEED_COUNT, $map);
            $json['data']  = ['list' => $operationGroupList];
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