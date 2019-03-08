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
        if ( is_null($loginType) || !in_array($loginType, LoginConfig::LOGIN_TYPE, true) ) {
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
        if ( !$userValid->scene($validScene)->check($validData) ) {
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
        if ( !$commonRes->isSuccess() ) {
            $json->setCode($commonRes->getErrCode());
            $json->setMsg($commonRes->getErrMsg());
        } else {
            $uid  = $this->getUid();
            $user = UserModel::get($uid);
            if ( !$user->isAdminUser() ) {
                $userService->logout();
                return json([
                    'code' => JsonReturnCode::DEFAULT_ERROR,
                    'msg'  => '账号或密码错误或没有登录权限',]);
            }
            $json->setData([
                'uid'  => (int)$user->getData('id'),
                'nick' => $user->getData('nick') ?? "$user->getData('id')}",
            ]);
        }
        return json($json);
    }

    /**
     * @route /userList
     */
    public function list()
    {
        $page   = (int)Request::get('page');
        $limit  = (int)Request::get('limit');
        $filter = Request::get('filter');
        $json   = [
            'code' => JsonReturnCode::SUCCESS,
        ];
        try {
            $map = [
            ];
            if ( is_array($filter) ) {
                if ( key_exists('search', $filter) && $filter['search'] ) {
                    $map[] = ['account|nick|phone|email', 'like', '%' . trim($filter['search']) . '%'];
                }
            }
            $field = ['id', 'account', 'nick', 'avatar', 'phone', 'email', 'gender', 'ctime'];
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
                    'isAdmin' => $userItem->isAdminUser(),
                    'avatar'  => $userItem->avatarModel()->field(['id', 'url', 'ctime', 'title'])->find(),
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
        array_map(function ($v) use ($validData) {
            if ( is_null($v) ) {
                unset($validData[$v]);
            }
        }, ['email', 'phone', 'profile', 'nick']);
        if ( !$userValid->scene(UserValid::SCENE['add'])->check($validData) ) {
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
            unset($addData['rePwd'],$addData['isAdmin']);
            UserModel::startTrans();
            [$addSuccess, $insertId] = UserModel::addItem($addData, UserModel::NEED_INSERT_ID);
            if ( $addSuccess ) {
                if ( $validData['isAdmin'] ) {
                    if ( !UserAdmin::addItem([
                        'uid'                  => $insertId,
                        'operation_list'       => [],
                        'operation_group_list' => [],
                    ]) ) {
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
        if ( in_array(UserModel::SUPER_USER_ID, $idArr) ) {
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
            if ( $selectCount !== $userCount ) {
                return json([
                    'code' => JsonReturnCode::INVAILD_PARAM,
                    'msg'  => '请输入未删除的用户id',
                ]);
            }
            UserModel::startTrans();
            foreach ($userList as $user) {
                if ( !is_null($user->dtime) ) {
                    $json['code'] = JsonReturnCode::INVAILD_PARAM;
                    $json['msg']  = 'ID: ' . $user->id . '用户已被删除,请输入未删除的用户id';
                    return json($json);
                }
                if ( !$user->nowDelete() ) {
                    $json['code'] = JsonReturnCode::SERVER_ERROR;
                    $json['msg']  = '删除ID: ' . $user->id . '用户失败';
                    return json($json);
                }
                if ( $user->isAdminUser() ) {
                    if ( !$user->adminInfo->nowDelete() ) {
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

    /**
     * 获取普通用户详情
     * @route /adminGetUserInfo get
     * @return \think\response\Json
     * @throws \think\Exception\DbException
     */
    public function generalUserInfo(): \think\response\Json
    {
        $json       = [
            'code' => 0,
        ];
        $uid        = (int)Request::get('id');
        $user       = UserModel::get($uid);
        $oriAddress = $user->address;

        $data         = [
            'id'      => $uid,
            'nick'    => $user->nick,
            'avatar'  => $user->avatar ? $user->avatarModel->url : null,
            'account' => $user->account,
            'email'   => $user->email,
            'gender'  => (int)$user->getData('gender'),
            'phone'   => $user->phone,
            'address' => $oriAddress ? $oriAddress->buildFormatAddress() : null,
            'profile' => $user->profile,
        ];
        $json['data'] = $data;
        return json($json);
    }

    /**
     * 编辑普通用户信息
     * @param UserService $userService
     * @param UserValid $userValid
     * @return \think\response\Json
     * @throws \think\Exception\DbException
     */
    public function editGeneralUserInfo(UserService $userService, UserValid $userValid): \think\response\Json
    {
        $email = Request::post('email');
        $email = $email === '' ? null : $email;
        $phone = Request::post('phone');
        $phone = $phone === '' ? null : $phone;
        $uid   = (int)Request::post('id');
        $user  = UserModel::get($uid);
        $admin = UserModel::get($this->getUid());
        if ( $user->isAdminUser() && !$admin->isRootUser() ) {
            return json([
                'code' => JsonReturnCode::UNAUTHORIZED,
                'msg'  => '普通管理员无法修改其他管理员信息',
            ]);
        }
        $validData = [
            'editNick'    => Request::post('nick'),
            'editAvatar'  => Request::post('avatar'),
            'editEmail'   => Request::post('email'),
            'editPhone'   => Request::post('phone'),
            'editAddress' => Request::post('address'),
            'profile'     => Request::post('profile'),
        ];
        if ( $user->nick === $validData['editNick'] ) {
            unset($validData['editNick']);
        }
        if ( $user->email === $validData['editEmail'] ) {
            unset($validData['editEmail']);
        }
        if ( $user->phone === $validData['editPhone'] ) {
            unset($validData['editPhone']);
        }
        if ( !$userValid->scene(UserValid::SCENE['editInfo'])->check($validData) ) {
            return json([
                'code' => JsonReturnCode::VALID_ERROR,
                'msg'  => $userValid->getError(),
            ]);
        }
        $json = [
            'code' => JsonReturnCode::SUCCESS,
        ];
        try {
            $user->nick    = $validData['editNick'] ?? $user->nick;
            $user->email   = $validData['editEmail'] ?? $user->email;
            $user->phone   = $validData['editPhone'] ?? $user->phone;
            $user->gender  = Request::post('gender') ?? (int)$user->getData('gender');
            $user->profile = $validData['profile'];
            $avatar        = Request::post('avatar');
            if ( is_array($avatar) ) {
                $user->avatar = $avatar['picId'];
            } else {
                if ( $avatar !== $user->getData('avatar') ) {
                    $user->avatar = $avatar;
                }
            }
            $user->save();
            if ( $user->address && !empty($validData['editAddress']) ) {
                $user->address->updateAddress(new Address($validData['editAddress']));
            }
        } catch (Exception $ex) {
            Bufflog::sqlException($ex);
            $json['code'] = JsonReturnCode::TP_DB_ERROR;
            $json['msg']  = $ex->getMessage();
        }
        return json($json);
    }
}
