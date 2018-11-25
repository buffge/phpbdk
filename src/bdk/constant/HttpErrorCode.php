<?php

/*
 * Author: buff <admin@buffge.com>
 * Created on : 2018-6-12, 15:59:31
 * QQ:1515888956
 */
namespace buffge\constant;

abstract class HttpErrorCode
{
    /**
     * 错误的请求
     */
    const
            BAD_REQUEST = 400;
    /**
     * 未授权的请求
     */
    const
            NOT_AUTH_REQUEST = 401;
    /**
     * 页面未找到
     */
    const
            NOT_FOUND = 404;
    /**
     * 服务器错误
     */
    const
            SERVER_ERROR = 500;
}
