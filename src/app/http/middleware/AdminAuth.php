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
 * 判断是否为管理员,不是则返回错误
 * Class AdminAuth
 * @package app\http\middleware
 */
class AdminAuth extends Base
{
    public function handle(Request $request, Closure $next)
    {
        if (!$this->isAdmin()) {
            return $this->needAdminAuth();
        }
        return $next($request);
    }

    private function needAdminAuth()
    {
        return json([
            'code' => JsonReturnCode::UNAUTHORIZED,
            'msg'  => '你没有权限进行此操作',
        ]);
    }
}