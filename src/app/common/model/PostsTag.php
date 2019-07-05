<?php


namespace bdk\app\common\model;

/**
 * 文章标签
 * Class PostsTag
 * @package bdk\app\common\model
 */
class PostsTag extends Base
{
    protected $field = [
        'id', 'ctime', 'utime', 'dtime',
        'name', 'posts_id',
    ];
    protected $json  = [];
}
