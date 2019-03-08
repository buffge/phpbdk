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

    /**
     * 身份证第一张图片
     * @return \think\model\relation\HasOne
     */
    public function idCardFirstPicModel(): \think\model\relation\HasOne
    {
        return $this->hasOne(Picture::class, 'id', 'id_card_first_pic_id');
    }

    /**
     * 身份证第二张图片
     * @return \think\model\relation\HasOne
     */
    public function idCardSecondPicModel(): \think\model\relation\HasOne
    {
        return $this->hasOne(Picture::class, 'id', 'id_card_second_pic_id');
    }
}
