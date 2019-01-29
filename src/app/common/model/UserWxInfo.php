<?php
/**
 * Created by IntelliJ IDEA.
 * User: buff
 * Date: 2018/12/29
 * Time: 17:52.
 */

namespace bdk\app\common\model;

/**
 * 用户微信信息
 * Class UserWxInfo
 * @package bdk\app\common\model
 */
class UserWxInfo extends Base
{
    protected $field = [
        'id', 'ctime', 'utime', 'dtime',
        'uid', 'nick', 'openid', 'unionid',
        'sex', 'city', 'province', 'country',
        'head_img_url',
    ];
    protected $json  = [];
}
