<?php

/*
 * Author: buff <admin@buffge.com>
 * Created on : 2018-11-15, 23:32:06
 * QQ:1515888956
 */
namespace bdk\traits;

use bdk\Register as RegisterTree;

trait Register
{
    public static function regInstance()
    {
        if (RegisterTree::isset(static::class)) {
            return RegisterTree::get(static::class);
        } else {
            $instance = new static;
            RegisterTree::push(static::class, $instance);
            return $instance;
        }
    }
}
