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
    const SUCCESS       = 0x0;
    /**
     * 默认错误
     */
    const DEFAULT_ERROR = -1;

    #错误常量
    /**
     * json解析错误
     */
    const JSON_ERROR        = 0xe1;
    /**
     * 数据库错误
     */
    const DB_ERROR          = 0xe2;
    /**
     * tp框架orm操作失败
     */
    const TP_DB_ERROR       = 0xe3;
    /**
     * 不允许的值,有时候表单有些值是枚举类型不允许
     */
    const NOT_ALLOW_VALUE   = 0xe4;
    /**
     * 操作频率太快
     */
    const HIGH_FREQUENCY    = 0xe5;
    /**
     * 验证错误 eg.验证码错误
     */
    const VALID_ERROR       = 0xe6;
    /**
     * 错误的参数
     */
    const ERROR_PARAM       = 0xe7;
    /**
     * 不是有效的参数
     */
    const INVAILD_PARAM     = 0xe8;
    /**
     * 缺少参数
     */
    const MISSING_ARGUMENTS = 0xe9;
    /**
     * 错误的方法
     */
    const ERROR_METHOD      = 0xea;
    /**
     * 服务器错误
     */
    const SERVER_ERROR      = 0xeb;
    /**
     * 未改变,常用于更新操作
     */
    const NO_CHANGE         = 0xec;
}
