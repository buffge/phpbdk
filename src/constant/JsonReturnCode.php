<?php

/*
 * Author: buff <admin@buffge.com>
 * Created on : 2018-12-9, 20:44:49
 * QQ:1515888956
 */

namespace bdk\constant;

abstract class JsonReturnCode
{
    /*
     * 成功
     */
    public const SUCCESS = 0x0;
    /**
     * 默认错误
     */
    public const DEFAULT_ERROR = -1;

    #错误常量
    /**
     * json解析错误
     */
    public const JSON_ERROR = 0xe1;
    /**
     * 数据库错误
     */
    public const DB_ERROR = 0xe2;
    /**
     * tp框架orm操作失败
     */
    public const TP_DB_ERROR = 0xe3;
    /**
     * 不允许的值,有时候表单有些值是枚举类型不允许
     */
    public const NOT_ALLOW_VALUE = 0xe4;
    /**
     * 操作频率太快
     */
    public const HIGH_FREQUENCY = 0xe5;
    /**
     * 验证错误 eg.验证码错误
     */
    public const VALID_ERROR = 0xe6;
    /**
     * 错误的参数
     */
    public const ERROR_PARAM = 0xe7;
    /**
     * 不是有效的参数
     */
    public const INVAILD_PARAM = 0xe8;
    /**
     * 缺少参数
     */
    public const MISSING_ARGUMENTS = 0xe9;
    /**
     * 错误的方法
     */
    public const ERROR_METHOD = 0xea;
    /**
     * 服务器错误
     */
    public const SERVER_ERROR = 0xeb;
    /**
     * 未改变,常用于更新操作
     */
    public const NO_CHANGE = 0xec;
    /**
     * 未授权的
     */
    public const UNAUTHORIZED = 0xed;
}
