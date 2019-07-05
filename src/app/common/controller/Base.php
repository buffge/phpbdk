<?php
/**
 * Created by IntelliJ IDEA.
 * User: buff
 * Date: 2019/1/2
 * Time: 15:18
 */

namespace bdk\app\common\controller;

use app\common\model\AppSession as AppSessionModel;
use bdk\app\common\model\User as UserModel;
use bdk\constant\JsonReturnCode;
use bdk\model\Log as BuffLog;
use Exception;
use think\Controller;
use think\facade\{Session,};
use think\facade\Request;

class Base extends controller
{
    protected function isLogin(): bool
    {
        if ( Request::has('session', 'header') ) {
            $userModel = UserModel::regInstance();
            return $userModel->checkAppIsLogin(Request::header('session'));
        }
        return Session::has('isLogin');
    }

    protected function getUid(): int
    {
        if ( Request::has('session', 'header') ) {
            return AppSessionModel::getValue(['session' => Request::header('session')], 'uid');
        }
        return Session::get('uid');
    }

    protected function isAdminUser(): bool
    {
        if ( !$this->isLogin() ) {
            return false;
        }
        $uid = $this->getUid();
        try {
            $user = UserModel::get($uid);
            return $user->isAdminUser();
        } catch (Exception $ex) {
            BuffLog::sqlException($ex);
            return false;
        }

    }

    protected function assignToken()
    {
        $token = hash('sha256', random_bytes(32));
        Session::set('token', $token);
        $this->assign('token', $token);
    }

    protected function assignCss($cssList)
    {
        if ( is_string($cssList) ) {
            $cssList = [$cssList];
        }
        $this->assign('cssList', $cssList);
    }

    protected function assignTitle(string $title)
    {
        $this->assign('title', $title);
    }

    protected function assignKeyWord(string $keywords)
    {
        $this->assign('keywords', $keywords);
    }

    protected function assignDescription(string $description)
    {
        $this->assign('description', $description);
    }

    /**
     * 分配head原始数据,会原样输出
     * @param string $raw
     */
    protected function assignRaw(string $raw)
    {
        $this->assign('raw', $raw);
    }

    /**
     * 返回缺少必须的参数 resp
     * @return \think\response\Json
     */
    protected function missArgs(): \think\response\Json
    {
        return json([
            'code' => JsonReturnCode::MISSING_ARGUMENTS,
            'msg'  => '缺少必要的参数',
        ]);
    }
}