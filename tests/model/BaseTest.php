<?php

/*
 * Author: buff <admin@buffge.com>
 * Created on : 2018-12-12, 21:43:09
 * QQ:1515888956
 */
namespace bdk\tests;

use PHPUnit\Framework\TestCase;
use app\common\model\User;

class BaseTest extends TestCase
{
    public function testPushAndPop()
    {
        $stack = [];
        $this->assertEquals(0, count($stack));
        $count = User::getCount();
        $this->assertEquals(4, $count);
        array_push($stack, 'foo');
        $this->assertEquals('foo', $stack[count($stack) - 1]);
        $this->assertEquals(1, count($stack));

        $this->assertEquals('foo', array_pop($stack));
        $this->assertEquals(0, count($stack));
    }

}
