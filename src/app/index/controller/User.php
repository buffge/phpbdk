<?php
/**
 * Created by IntelliJ IDEA.
 * User: buff
 * Date: 2019/1/2
 * Time: 15:27
 */

namespace bdk\app\index\controller;

use bdk\app\common\controller\Base;
use bdk\app\common\model\{Log as BuffLog, User as UserModel,};
use bdk\app\common\model\json\Address;
use bdk\app\common\model\json\JsonResult;
use bdk\app\common\service\{Mail as MailService, Session as SessService, User as UserService};
use bdk\app\common\service\json\{LoginConfig, RegisterConfig,};
use bdk\app\common\validate\{User as UserValid,};
use bdk\constant\JsonReturnCode;
use Exception;
use think\facade\Request;

class User extends Base
{
    /**
     * @route /user/register
     * @param UserService $userService
     * @param UserValid $userValid
     * @return \think\response\Json
     */
    public function register(UserService $userService, UserValid $userValid)
    {
        $json = new JsonResult;
        try {
            $validData    = [
                'account'                 => Request::post('uname'),
                'pwd'                     => Request::post('pwd'),
                'rePwd'                   => Request::post('rePwd'),
                'email'                   => Request::post('email'),
                'emailRegisterEmail'      => Request::post('email'),
                'emailRegisterVerifyCode' => Request::post('emailVerifiyCode'),
            ];
            $registerType = Request::post('registerType');
            if ( is_null($registerType) || !in_array($registerType,
                    RegisterConfig::REGISTER_TYPE, true) ) {
                return json([
                    'code' => JsonReturnCode::DEFAULT_ERROR,
                    'msg'  => '注册方式不正确',
                ]);
            }
            $validScene = null;
            switch ($registerType) {
                case RegisterConfig::REGISTER_TYPE['EMAIL_CODE_REGISTER']:
                    $validScene = UserValid::SCENE['emailRegister'];
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
            $registerConf = new RegisterConfig;
            $registerConf->setRegisterType($registerType);
            $registerConf->setEmail(trim($validData['email']));
            $registerConf->setAccount(trim($validData['account']));
            $registerConf->setPwd(trim($validData['pwd']));
            $commonRes = $userService->register($registerConf);
            if ( !$commonRes->isSuccess() ) {
                $json->setCode($commonRes->getErrCode());
                $json->setMsg($commonRes->getErrMsg());
            }
        } catch (Exception $ex) {
            $json->setMsg($ex->getMessage());
            $json->setCode(JsonReturnCode::SERVER_ERROR);
        }
        return json($json);
    }

    /**
     * @route /login
     */
    public function login(UserService $userService, UserValid $userValid)
    {
        $validData = [
            'commonUserLoginAccount' => Request::post('account'),
            'loginPwd'               => Request::post('pwd'),
            'accountVerifyCode'      => Request::post('accountVerifyCode'),
            'emailLoginEmail'        => Request::post('email'),
            'emailLoginVerifyCode'   => Request::post('emailLoginVerifyCode'),
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
                $validScene = UserValid::SCENE['commonUserAccountLogin'];
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
        $loginConf->setAccount($validData['commonUserLoginAccount']);
        $loginConf->setPwd($validData['loginPwd']);
        $loginConf->setAccountVerifyCode($validData['accountVerifyCode']);
        $loginConf->setEmail($validData['emailLoginEmail']);
        $loginConf->setEmailVerifyCode($validData['emailLoginVerifyCode']);
        $commonRes = $userService->login($loginConf);
        if ( !$commonRes->isSuccess() ) {
            $json->setCode($commonRes->getErrCode());
            $json->setMsg($commonRes->getErrMsg());
        } else {
            $userDetail = UserModel::get($this->getUid());
            $json->setData([
                'uid'  => (int)$userDetail->getData('id'),
                'nick' => $userDetail->getData('nick') ?? "未命名用户{$userDetail->getData('id')}",
            ]);
        }
        return json($json);
    }

    /**
     * 退出
     * @param SessService $sessService
     */
    public function logout(SessService $sessService)
    {
        $sessService->logout();
        return json(['code' => JsonReturnCode::SUCCESS]);
    }

    /**
     * @route /modifyPwd
     * @param UserService $userService
     * @param UserValid $userValid
     */
    public function modifyPwd(UserService $userService, UserValid $userValid)
    {
        $uid       = $this->getUid();
        $validData = [
            'modifyOriPwd' => Request::post('oriPwd'),
            'modifyNewPwd' => Request::post('newPwd'),
            'modifyRePwd'  => Request::post('rePwd'),
        ];
        if ( !$userValid->scene(UserValid::SCENE['modifyPwd'])->check($validData) ) {
            return json([
                'code' => JsonReturnCode::VALID_ERROR,
                'msg'  => $userValid->getError(),
            ]);
        }
        $json = [
            'code' => JsonReturnCode::SUCCESS,
        ];
        try {
            $user      = UserModel::get($uid);
            $user->pwd = $userService->buildHashPwd($validData['modifyNewPwd']);
            if ( !$user->save() ) {
                $json['code'] = JsonReturnCode::NO_CHANGE;
                $json['msg']  = '无更新';
            }
        } catch (Exception $ex) {
            $json['code'] = JsonReturnCode::SERVER_ERROR;
            $json['msg']  = '服务器异常';
        }
        return json($json);

    }

    /**
     * @route /getUserInfo
     * @return \think\response\Json
     * @throws \think\Exception\DbException
     */
    public function info()
    {
        $json       = [
            'code' => 0,
        ];
        $uid        = $this->getUid();
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
     * @route /editUserInfo
     */
    public function editInfo(UserService $userService, UserValid $userValid)
    {
        $email     = Request::post('email');
        $email     = $email === '' ? null : $email;
        $phone     = Request::post('phone');
        $phone     = $phone === '' ? null : $phone;
        $uid       = $this->getUid();
        $user      = UserModel::get($uid);
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

    /**
     * @route /checkUniqueFieldExist
     */
    public function checkUniqueFieldExist(string $name)
    {
        $field = trim(Request::post($name));
        $json  = [
            'code' => JsonReturnCode::SUCCESS,
            'data' => [],
        ];
        try {
            $isExist                 = UserModel::checkUniqueFieldExist($name, $field);
            $json['data']['isExist'] = $isExist;
        } catch (Exception $ex) {
            Bufflog::sqlException($ex);
            $json['code'] = JsonReturnCode::TP_DB_ERROR;
            $json['msg']  = $ex->getMessage();
        }

        return json($json);

    }

    /**
     * @route /user/register/sendMailCode
     * @param MailService $mailService
     * @return \think\response\Json
     */
    public function sendEmailRegisterVerifyCode(MailService $mailService, SessService $sessService)
    {
        $email = Request::post('email');
        $json  = [
            'code' => JsonReturnCode::SUCCESS,
        ];
        try {
            $verifyCode = mt_rand(1000, 9999);
            if ( $mailService->sendRegisterVerifyCode($email, $verifyCode) ) {
                $sessService->setRegisterSendVerifyCodeEmail($email);
                $sessService->setEmailRegisterSendVerifyCode($verifyCode);
            } else {
                throw new Exception('发送邮箱验证码失败');
            }
        } catch (Exception $ex) {
            $json['code'] = JsonReturnCode::SERVER_ERROR;
            $json['msg']  = $ex->getMessage();
        }
        return json($json);
    }
}