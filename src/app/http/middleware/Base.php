<?php

/**
 * Created by IntelliJ IDEA.
 * User: buff
 * Date: 2019/1/4
 * Time: 15:58
 */

namespace bdk\app\http\middleware;

use app\common\model\AppSession as AppSessionModel;
use bdk\app\common\model\User as UserModel;
use think\facade\Request;
use think\facade\Session;

class Base
{
    public function isLogin(): bool
    {
        if ( Request::has('session', 'header') ) {
            $userModel = UserModel::regInstance();
            return $userModel->checkAppIsLogin(Request::header('session'));
        }
        return Session::has('isLogin');
    }

    public function getUid(): ?int
    {
        if ( Request::has('session', 'header') ) {
            return AppSessionModel::getValue(['session' => Request::header('session')], 'uid');
        }
        return Session::get('uid');
    }

    public function isAdmin(): bool
    {
        $uid = $this->getUid();
        if ( !is_int($uid) ) {
            return false;
        }
        $user = UserModel::get($uid);
        return $user->isAdminUser($uid);
    }
}