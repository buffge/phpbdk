<?php
/**
 * Created by IntelliJ IDEA.
 * User: buff
 * Date: 2018/12/29
 * Time: 17:52.
 */

namespace bdk\app\common\model;
/**
 * 缩略图
 * Class Thumb
 * @package bdk\app\common\model
 */
class Thumb extends Base
{
    protected $field = [
        'id', 'ctime', 'utime', 'dtime',
        'url', 'path', 'width', 'height', 'size', 'picture_id',
    ];
    protected $json  = [];
}
