<?php
/**
 * Created by IntelliJ IDEA.
 * User: buff
 * Date: 2019/1/2
 * Time: 15:18
 */

namespace bdk\app\common\controller;

use bdk\app\common\model\User as UserModel;
use bdk\model\Log as BuffLog;
use Exception;
use think\Controller;
use think\facade\{Session,};

class Base extends controller
{
    protected function isLogin(): bool
    {
        return Session::has('uid');
    }

    protected function getUid(): int
    {
        return Session::get('uid');
    }

    protected function isAdminUser(): bool
    {
        if (!$this->isLogin()) {
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
        if (is_string($cssList)) {
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
}