<?php
/**
 * Created by IntelliJ IDEA.
 * User: buff
 * Date: 2019/1/3
 * Time: 14:02
 */

namespace bdk\app\admin\controller;

use bdk\app\common\controller\Base;
use bdk\app\common\model\{Log as BuffLog, User as UserModel, UserAdmin};
use bdk\app\common\model\json\JsonResult;
use bdk\app\common\service\json\LoginConfig;
use bdk\app\common\service\User as UserService;
use bdk\app\common\validate\{User as UserValid,};
use bdk\constant\JsonReturnCode;
use Exception;
use think\facade\Request;

class User extends Base
{
    /**
     * @route /admin/login
     * @param UserService $userService
     * @param UserValid $userValid
     * @return \think\response\Json
     * @throws \think\Exception\DbException
     */
    public function login(UserService $userService, UserValid $userValid)
    {
        $validData = [
            'loginAccount'         => Request::post('account'),
            'loginPwd'             => Request::post('pwd'),
            'accountVerifyCode'    => Request::post('accountVerifyCode'),
            'emailLoginEmail'      => Request::post('email'),
            'emailLoginVerifyCode' => Request::post('emailLoginVerifyCode'),
        ];
        $loginType = Request::post('loginType');
        if (is_null($loginType) || !in_array($loginType, LoginConfig::LOGIN_TYPE, true)) {
            return json([
                'code' => JsonReturnCode::DEFAULT_ERROR,
                'msg'  => '登录方式不正确',
            ]);
        }
        $validScene = null;
        switch ($loginType) {
            case LoginConfig::LOGIN_TYPE['ACCOUNT_LOGIN']:
                $validScene = UserValid::SCENE['accountLogin'];
                break;
            default:
                break;
        }
        if (!$userValid->scene($validScene)->check($validData)) {
            return json([
                'code' => JsonReturnCode::VALID_ERROR,
                'msg'  => $userValid->getError(),
            ]);
        }
        $json      = new JsonResult;
        $loginConf = new LoginConfig;
        $loginConf->setLoginType($loginType);
        $loginConf->setAccount($validData['loginAccount']);
        $loginConf->setPwd($validData['loginPwd']);
        $loginConf->setAccountVerifyCode($validData['accountVerifyCode']);
        $loginConf->setEmail($validData['emailLoginEmail']);
        $loginConf->setEmailVerifyCode($validData['emailLoginVerifyCode']);
        $commonRes = $userService->login($loginConf);
        if (!$commonRes->isSuccess()) {
            $json->setCode($commonRes->getErrCode());
            $json->setMsg($commonRes->getErrMsg());
        } else {
            $uid  = $this->getUid();
            $user = UserModel::get($uid);
            if (!$user->isAdminUser()) {
                $userService->logout();
                return json([
                    'code' => JsonReturnCode::DEFAULT_ERROR,
                    'msg'  => '账号或密码错误或没有登录权限',]);
            }
        }
        return json($json);
    }

    /**
     * @route /userList
     */
    public function list()
    {
        $page   = Request::get('page');
        $limit  = Request::get('limit');
        $filter = Request::get('filter');
        $json   = [
            'code' => JsonReturnCode::SUCCESS,
        ];
        try {
            $map = [
                ['dtime', 'null', ''],
            ];
            if (is_array($filter)) {
                if (key_exists('search', $filter) && $filter['search']) {
                    $map[] = ['account|nick|phone|email', 'like', '%' . trim($filter['search']) . '%'];
                }
            }
            $field = ['id', 'account', 'nick', 'phone', 'email', 'gender', 'ctime'];
            $order = ['ctime' => 'desc'];
            [$userList, $count] = UserModel::getList($page, $limit, UserModel::NEED_COUNT, $map, $field, $order);
            $resList = [];
            foreach ($userList as $userItem) {
                $resList[] = [
                    'id'      => $userItem->id,
                    'account' => $userItem->account,
                    'nick'    => $userItem->nick,
                    'phone'   => $userItem->phone,
                    'email'   => $userItem->email,
                    'gender'  => $userItem->gender,
                    'address' => $userItem->address,
                    'ctime'   => $userItem->ctime,
                ];
            }
            $json['data']  = [
                'list' => $resList,
            ];
            $json['page']  = $page;
            $json['limit'] = $limit;
            $json['count'] = $count;
        } catch (Exception $ex) {
            $json['code'] = JsonReturnCode::SERVER_ERROR;
            $json['msg']  = $ex->getMessage();
            BuffLog::sqlException($ex);
        }
        return json($json);
    }

    /**
     * @route /addUser
     */
    public function add(UserService $userService, UserValid $userValid)
    {
        $uid       = $this->getUid();
        $user      = UserModel::get($uid);
        $validData = [
            'account' => Request::post('account'),
            'pwd'     => Request::post('pwd'),
            'rePwd'   => Request::post('rePwd'),
            'nick'    => Request::post('nick'),
            'email'   => Request::post('email'),
            'phone'   => Request::post('phone'),
            'profile' => Request::post('profile'),
            'isAdmin' => Request::post('isAdmin'),
        ];
        array_map(function ($v) {
            if (is_null($v)) {
                unset($validData[$v]);
            }
        }, ['email', 'phone', 'profile', 'nick']);
        if (!$userValid->scene(UserValid::SCENE['add'])->check($validData)) {
            return json([
                'code' => JsonReturnCode::VALID_ERROR,
                'msg'  => $userValid->getError(),
            ]);
        }
        $json = [
            'code' => JsonReturnCode::SUCCESS,
        ];
        try {
            $addData        = $validData;
            $addData['pwd'] = $userService->buildHashPwd($addData['pwd']);
            unset($addData['rePwd']);
            unset($addData['isAdmin']);
            UserModel::startTrans();
            [$addSuccess, $insertId] = UserModel::addItem($addData, UserModel::NEED_INSERT_ID);
            if ($addSuccess) {
                if ($validData['isAdmin']) {
                    if (!UserAdmin::addItem([
                        'uid'                  => $insertId,
                        'operation_list'       => [],
                        'operation_group_list' => [],
                    ])) {
                        UserModel::rollback();
                        $json['code'] = JsonReturnCode::SERVER_ERROR;
                        $json['msg']  = '将用户添加到管理员表失败';
                    } else {
                        UserModel::commit();
                    }
                } else {
                    UserModel::commit();
                }
            } else {
                $json['code'] = JsonReturnCode::SERVER_ERROR;
                $json['msg']  = '添加到用户表失败';
            }
        } catch (Exception $ex) {
            Bufflog::sqlException($ex);
            $json['code'] = JsonReturnCode::SERVER_ERROR;
            $json['msg']  = $ex->getMessage();
        }
        return json($json);
    }

    /**
     * @route /deleteUsers
     */
    public function delete(UserService $userService)
    {
        $idArr = Request::post('idArr');
        $json  = [
            'code' => JsonReturnCode::SUCCESS,
        ];
        if (in_array(UserModel::SUPER_USER_ID, $idArr)) {
            return json([
                'code' => JsonReturnCode::INVAILD_PARAM,
                'msg'  => '不可以删除超级管理员',
            ]);
        }
        try {
            $userCount = count($idArr);
            $map       = [
                ['dtime', 'null', null],
                ['id', 'in', $idArr],
            ];
            [$userList, $selectCount] = UserModel::getList(UserModel::NOT_LIMIT, UserModel::NOT_LIMIT,
                UserModel::NEED_COUNT, $map);
            if ($selectCount !== $userCount) {
                return json([
                    'code' => JsonReturnCode::INVAILD_PARAM,
                    'msg'  => '请输入未删除的用户id',
                ]);
            }
            UserModel::startTrans();
            foreach ($userList as $user) {
                if (!is_null($user->dtime)) {
                    $json['code'] = JsonReturnCode::INVAILD_PARAM;
                    $json['msg']  = 'ID: ' . $user->id . '用户已被删除,请输入未删除的用户id';
                    return json($json);
                }
                if (!$user->nowDelete()) {
                    $json['code'] = JsonReturnCode::SERVER_ERROR;
                    $json['msg']  = '删除ID: ' . $user->id . '用户失败';
                    return json($json);
                }
                if ($user->isAdminUser()) {
                    if (!$user->adminInfo->nowDelete()) {
                        $json['code'] = JsonReturnCode::SERVER_ERROR;
                        $json['msg']  = '删除ID: ' . $user->id . '管理员用户失败';
                        UserModel::rollback();
                        return json($json);
                    }
                }
            }
            UserModel::commit();
        } catch (Exception $ex) {
            Bufflog::sqlException($ex);
            $json['code'] = JsonReturnCode::SERVER_ERROR;
            $json['msg']  = $ex->getMessage();
        }
        return json($json);
    }
}