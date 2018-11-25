<?php

/*
 * Author: buff <admin@buffge.com>
 * Created on : 2018-11-14, 10:10:41
 * QQ:1515888956
 */
namespace bdk\traits;

use ReflectionClass;

/**
 * 导出类常量
 */
trait ExportConstant
{
    public static function exportConstant(): array
    {
        return (new ReflectionClass(static::class))->getConstants();
    }
}
