<?php

/*
 * Author: buff <admin@buffge.com>
 * Created on : 2018-12-9, 20:40:21
 * QQ:1515888956
 */
namespace bdk\model;

use Exception;

/**
 * 日志
 */
class Log extends Base
{
    const LEVEL_DEBUG     = 'debug';
    const LEVEL_INFO      = 'info';
    const LEVEL_NOTICE    = 'notice';
    const LEVEL_WARNING   = 'warning';
    const LEVEL_ERROR     = 'error';
    const LEVEL_CRITICAL  = 'critical';
    const LEVEL_ALERT     = 'alert';
    const LEVEL_EMERGENCY = 'emergency';
    /**
     * 这是前端显示用的
     */
    const TYPE_ALL        = 'all';
    const TYPE_SEND_SMS   = 'sendSms';
    const TYPE_WX_API     = 'wxApi';
    /**
     * search_0 => 异常所在文件
     * search_1 = 异常信息所在行;
     * search_2 = traceString;
     */
    const TYPE_SQL        = 'sql';
    /**
     * 爬虫类型
     * LEVEL_ERROR::search_0 => 异常信息
     * LEVEL_ERROR::search_1 = 异常信息所在行;
     * LEVEL_ERROR::search_2 = traceString;
     * LEVEL_NOTICE::search_0 => 插入的数据条数
     */
    const TYPE_CMS_SPIDER = 'cmsSpider';

    /**
     * sql异常日常日志
     * @param type $ex
     */
    public static function sqlException($ex)
    {
        $logRecord = [
            'level'    => static::LEVEL_WARNING,
            'type'     => static::TYPE_SQL,
            'msg'      => $ex->getMessage(),
            'search_0' => $ex->getFile(),
            'search_1' => $ex->getLine(),
            'search_2' => $ex->getTraceAsString(),
        ];
        self::addItem($logRecord);
    }

}
