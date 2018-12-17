<?php

/*
 * Author: buff <admin@buffge.com>
 * Created on : 2018-11-30, 0:05:37
 * QQ:1515888956
 */
namespace bdk\traits;

/**
 * 禁止实例化
 */
trait DisableInstantiation
{
    protected function __construct()
    {
        
    }

    protected function __clone()
    {
        
    }

}
