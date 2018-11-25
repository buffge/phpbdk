<?php

/*
 * Author: buff <admin@buffge.com>
 * Created on : 2018-6-11, 10:40:44
 * QQ:1515888956
 */
namespace bdk\constant;

abstract class Common
{
    /*
     * 成功
     */
    const SUCCESS = 0x0;
    /**
     * 标记 临时标记用的没有实际含义
     */
    const FLAG = 0x1;
    /**
     * 默认值
     */
    const DEFAULT = -1;
    /**
     * 默认错误
     */
    const DEFAULT_ERROR = -1;
    /**
     * 未定义
     */
    const UNDEFINED = -1;
    /**
     * 需要分页获取列表的时候返回总数量
     */
    const NEED_COUNT = true;
    /**
     * 需要分页获取列表的时候不返回总数量
     */
    const NOT_NEED_COUNT = false;
    /**
     * 显示is_del为已删除的数据
     */
    const SHOW_DEL = true;
    /**
     * 不限制
     */
    const NOT_LIMIT = -1;
    /**
     * 空字符串
     */
    const EMPTY_STR = '';
    #错误常量
    /**
     * json解析错误
     */
    const JSON_ERROR = 0xe1;
    /**
     * 数据库错误
     */
    const DB_ERROR = 0xe2;
    /**
     * tp框架orm操作失败
     */
    const TP_DB_ERROR = 0xe3;
    /**
     * 不允许的值,有时候表单有些值是枚举类型不允许
     */
    const NOT_ALLOW_VALUE = 0xe4;
    /**
     * 操作频率太快
     */
    const HIGH_FREQUENCY = 0xe5;
    /**
     * 验证错误 eg.验证码错误
     */
    const VALID_ERROR = 0xe6;
    /**
     * 错误的参数
     */
    const ERROR_PARAM = 0xe7;
    /**
     * 不是有效的参数
     */
    const INVAILD_PARAM = 0xe8;
    /**
     * 缺少参数
     */
    const MISSING_ARGUMENTS = 0xe9;
    /**
     * 错误的方法
     */
    const ERROR_METHOD = 0xea;
    /**
     * 服务器错误
     */
    const SERVER_ERROR = 0xeb;
    /**
     * 未改变,常用于更新操作
     */
    const NO_CHANGE = 0xec;
}
