<?php

/*
 * Author: buff <admin@buffge.com>
 * Created on : 2018-6-11, 10:49:28
 * QQ:1515888956
 */

namespace bdk\exception;

use Exception;

/**
 * 注册树模式异常
 */
class RegisterModeException extends Exception
{
    /**
     * 别名(键值)已经存在
     */
    const ALIAS_ALREADY_EXISTED = 0xe0;
    /**
     * 别名(键值)不存在
     */
    const ALIAS_NOT_FOUND = 0xe1;
}
