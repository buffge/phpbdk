<?php

/*
 * Author: buff <admin@buffge.com>
 * Created on : 2018-11-15, 20:32:18
 * QQ:1515888956
 */
/**
 *
 */
function buff_composer()
{
    echo "buff Composer init success!\n";
}
/**
 * 
 * @param type $min
 * @param type $max
 * @return float
 */
function random_float($min = 0, $max = 1): float
{
    return $min + mt_rand() / mt_getrandmax() * ($max - $min);
}
