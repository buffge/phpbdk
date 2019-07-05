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
    /**
     * 订阅来源
     */
    public const SUBSCRIBE_SCENE_ZH = [
        'ADD_SCENE_SEARCH'            => '公众号搜索',
        'ADD_SCENE_ACCOUNT_MIGRATION' => '公众号迁移',
        'ADD_SCENE_PROFILE_CARD'      => '名片分享',
        'ADD_SCENE_QR_CODE'           => '扫描二维码',
        'ADD_SCENEPROFILE LINK'       => '图文页内名称点击',
        'ADD_SCENE_PROFILE_ITEM'      => '图文页右上角菜单',
        'ADD_SCENE_PAID'              => '支付后关注',
        'ADD_SCENE_OTHERS'            => '其他',
    ];
    protected $field = [
        'id', 'ctime', 'utime', 'dtime',
        'uid', 'openid', 'nickname', 'sex',
        'city', 'country', 'province', 'language',
        'head_img_url', 'subscribe_time',
        'unionid', 'remark', 'groupid', 'tagid_list', 'subscribe_scene',
        'qr_scene', 'qr_scene_str',
    ];
    protected $json  = [
        'tagid_list',
    ];
}
