<?php

/*
 * Author: buff <admin@buffge.com>
 * Created on : 2018-12-19, 14:12:54
 * QQ:1515888956
 */

namespace bdk;

abstract class Common
{
    public static function isMobilePhone(string $phone): bool
    {
        return preg_match("~1[3456789]\d{9}~", $phone) === 1;
    }
}
