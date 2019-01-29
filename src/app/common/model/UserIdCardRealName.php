<?php
/**
 * Created by IntelliJ IDEA.
 * User: buff
 * Date: 2018/12/29
 * Time: 17:52.
 */

namespace bdk\app\common\model;


/**
 * 用户身份证实名认证表
 * Class UserIdCardRealName
 * @package app\common\model
 */
class UserIdCardRealName extends Base
{
    protected $field = [
        'id', 'ctime', 'utime', 'dtime',
        'uid', 'id_name', 'id_no', 'id_card_first_pic_id',
        'id_card_second_pic_id',
    ];
    protected $json  = [];

}
