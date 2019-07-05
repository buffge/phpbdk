<?php
/**
 * Created by IntelliJ IDEA.
 * User: buff
 * Date: 2019/3/12
 * Time: 14:28
 */

namespace bdk\app\common\model;


class AliSmsRecord extends Base
{
    protected $field = [
        'id', 'ctime', 'utime', 'dtime',
        'phone_numbers', 'sign_name', 'template_code', 'template_param',
        'result_msg', 'req_id', 'biz_id', 'result_code',
    ];

}