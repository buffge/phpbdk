<?php
/**
 * Created by IntelliJ IDEA.
 * User: buff
 * Date: 2018/12/29
 * Time: 17:52.
 */

namespace bdk\app\common\model;

class AppSession extends Base
{
    protected $field = [
        'id', 'ctime', 'utime', 'dtime',
        'uid', 'session', 'expire',
    ];

    /**
     * 生成appSession
     */
    public static function generateAppSession(): string
    {
        return bin2hex(random_bytes(32));
    }
}
