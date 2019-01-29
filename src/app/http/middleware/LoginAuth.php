<?php

/**
 * Created by IntelliJ IDEA.
 * User: buff
 * Date: 2019/1/4
 * Time: 15:58
 */

namespace bdk\app\http\middleware;

use bdk\constant\JsonReturnCode;
use Closure;
use think\Request;

/**
 * 判断是否为已登录,不是则返回错误
 * Class LoginAuth
 * @package bdk\app\http\middleware
 */
class LoginAuth extends Base
{
    public function handle(Request $request, Closure $next)
    {
        if (!$this->isLogin()) {
            return $this->needLogin();
        }
        return $next($request);
    }

    private function needLogin()
    {
        return json([
            'code' => JsonReturnCode::UNAUTHORIZED,
            'msg'  => '请先登录',
        ]);
    }
}