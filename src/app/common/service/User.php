<?php
/**
 * Created by IntelliJ IDEA.
 * User: buff
 * Date: 2019/1/2
 * Time: 15:16
 */

namespace bdk\app\common\service;

use bdk\app\common\model\User as UserModel;
use bdk\app\common\service\json\{CommonResult, LoginConfig, RegisterConfig,};
use bdk\constant\JsonReturnCode;
use bdk\exception\NotFoundException;
use bdk\model\Log as BuffLog;
use bdk\traits\Register;
use Exception;
use think\facade\Session as TpSession;

class User
{
    use Register;

    /**
     * @param RegisterConfig $conf
     * @return CommonResult
     */
    public function register(RegisterConfig $conf): CommonResult
    {
        $result = new CommonResult;
        try {
            switch ($conf->getRegisterType()) {
                case RegisterConfig::REGISTER_TYPE['ACCOUNT_REGISTER']:
                    $result = $this->accountRegister($conf->getAccount(), $conf->getPwd());
                    break;
                case RegisterConfig::REGISTER_TYPE['EMAIL_REGISTER']:
                    $result = $this->emailRegister($conf->getAccount(), $conf->getPwd(), $conf->getEmail());
                    break;
                case RegisterConfig::REGISTER_TYPE['PHONE_REGISTER']:
                    $result = $this->phoneRegister($conf->getAccount(), $conf->getPwd(), $conf->getPhone());
                    break;
                default:
                    $result = new CommonResult();
                    $result->setIsSuccess(false);
                    $result->setErrMsg("服务器错误,系统设置注册方式不正确");
                    break;
            }
        } catch (Exception $ex) {
            $result->setIsSuccess(false);
            $result->setErrCode(JsonReturnCode::DEFAULT_ERROR);
            $result->setErrMsg($ex->getMessage());
            BuffLog::sqlException($ex);
        }
        return $result;
    }

    /**
     * @param LoginConfig $conf
     * @return CommonResult
     */
    public function login(LoginConfig $conf): CommonResult
    {
        switch ($conf->getLoginType()) {
            case LoginConfig::LOGIN_TYPE['ACCOUNT_LOGIN']:
                return $this->accountLogin($conf->getAccount(), $conf->getPwd(),
                    $conf->getAccountVerifyCode());
            case LoginConfig::LOGIN_TYPE['EMAIL_VERIFY_CODE_LOGIN']:
                return $this->emailVerifyCodeLogin($conf->getEmail(), $conf->getEmailVerifyCode());
            case LoginConfig::LOGIN_TYPE['PHONE_VERIFY_CODE_LOGIN']:
                return $this->phoneVerifyCodeLogin($conf->getPhone(), $conf->getPhoneVerifyCode());
                break;
            default:
                $result = new CommonResult;
                $result->setIsSuccess(false);
                $result->setErrMsg("服务器错误,系统设置登录方式不正确");
                break;
        }
        return $result;
    }

    /**
     * @param string $email
     * @param string $emailVerifyCode
     * @return CommonResult
     */
    private function emailVerifyCodeLogin(string $email, string $emailVerifyCode): CommonResult
    {

    }

    /**
     * @param string $phone
     * @param string $code
     * @return CommonResult
     */
    private function phoneVerifyCodeLogin(string $phone, string $code): CommonResult
    {

    }

    /**
     * @param string $account
     * @param string $pwd
     * @param string $accountVerifyCode
     * @return CommonResult
     */
    private function accountLogin(string $account, string $pwd, string $accountVerifyCode): CommonResult
    {
        $res = new CommonResult;
        try {
            $user = UserModel::getDetail([
                ['account|email|phone', '=', $account],
            ]);
            if ( $this->verifyPwd($pwd, $user->pwd) ) {
                $uid            = $user->getUid();
                $sessionService = new Session;
                $sessionService->login($uid);
                $res->setIsSuccess(true);
            } else {
                $res->setErrMsg('账号或密码错误');
            }
        } catch (NotFoundException $ex) {
            $res->setIsSuccess(false);
            $res->setErrMsg('账号不存在');
        } catch (Exception $ex) {
            $res->setIsSuccess(false);
            $res->setErrMsg($ex->getMessage());
        }
        return $res;
    }

    /**
     * @param string $account
     * @param string $pwd
     * @return CommonResult
     */
    private function accountRegister(string $account, string $pwd): CommonResult
    {
        return $this->addUser($account, $pwd);
    }

    /**
     * @param string $account
     * @param string $pwd
     * @param string $email
     * @return CommonResult
     */
    private function emailRegister(string $account, string $pwd, string $email): CommonResult
    {
        return $this->addUser($account, $pwd, $email);

    }

    /**
     * @param string $account
     * @param string $pwd
     * @param string $phone
     * @return CommonResult
     */
    private function phoneRegister(string $account, string $pwd, string $phone): CommonResult
    {
        return $this->addUser($account, $pwd, $phone);

    }

    /**
     * @param string $account
     * @param string $pwd
     * @param string|null $email
     * @param string|null $phone
     * @return CommonResult
     */
    private function addUser(string $account, string $pwd, string $email = null, string $phone = null): CommonResult
    {
        $res     = new CommonResult;
        $addData = [
            'account' => $account,
            'pwd'     => $this->buildHashPwd($pwd),
        ];
        if ( !is_null($email) ) {
            $addData['email'] = $email;
        }
        if ( !is_null($phone) ) {
            $addData['phone'] = $phone;
        }
        $insertErrMsg = '插入数据库失败';
        try {
            $addSuccess = UserModel::addItem($addData);
            if ( !$addSuccess ) {
                $res->setIsSuccess(false);
                $res->setErrMsg($insertErrMsg);
            } else {
                $res->setIsSuccess(true);
            }
        } catch (Exception $ex) {
            $res->setIsSuccess(false);
            $res->setErrMsg($insertErrMsg);
            BuffLog::sqlException($ex);
        }
        return $res;

    }

    /**
     * 由明文密码生成hash密码
     * @param string $plainPwd
     * @return string
     */
    public function buildHashPwd(string $plainPwd): string
    {
        return password_hash($plainPwd, PASSWORD_ARGON2ID);
    }

    /**
     * 检查密码是否正确
     * @param string $plainPwd
     * @param string $hashPwd
     * @return bool
     */
    public function verifyPwd(string $plainPwd, string $hashPwd): bool
    {
        return password_verify($plainPwd, $hashPwd);
    }

    public function logout()
    {
        $sessionService = new Session;
        return $sessionService->logout();
    }

    /**
     * 验证用户密码是否正确
     * @param string $oriPwd
     * @return bool
     * @throws \think\Exception\DbException
     */
    public function validOriPwd(string $oriPwd): bool
    {
        $uid  = $this->getUid();
        $user = UserModel::get($uid);
        return $this->verifyPwd($oriPwd, $user->pwd);
    }

    public function getUid()
    {
        return TpSession::get('uid');
    }
}