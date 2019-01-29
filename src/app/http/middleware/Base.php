<?php

/**
 * Created by IntelliJ IDEA.
 * User: buff
 * Date: 2019/1/4
 * Time: 15:58
 */

namespace bdk\app\http\middleware;

use bdk\app\common\model\User as UserModel;
use think\facade\Session;

class Base
{
    public function isLogin(): bool
    {
        return Session::has('uid');
    }

    public function getUid(): ?int
    {
        return Session::get('uid');
    }

    public function isAdmin(): bool
    {
        $uid = $this->getUid();
        if (!is_int($uid)) {
            return false;
        }
        $user = UserModel::get($uid);
        return $user->isAdminUser($uid);
    }
}