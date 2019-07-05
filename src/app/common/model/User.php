<?php
/**
 * Created by IntelliJ IDEA.
 * User: buff
 * Date: 2018/12/29
 * Time: 17:52.
 */

namespace bdk\app\common\model;

use app\common\model\AppSession;
use bdk\app\common\model\Log as BuffLog;
use Exception;
use think\model\relation\HasOne;

/**
 * 用户
 * Class User
 * @package bdk\app\common\model
 */
class User extends Base
{
    protected $field = [
        'id', 'ctime', 'utime', 'dtime',
        'nick', 'avatar_pic_id', 'gender',
        'account', 'pwd', 'phone', 'email', 'profile',
    ];
    protected $json  = [
    ];
    /**
     * 超级管理员id
     */
    public const SUPER_USER_ID          = 0x1;
    public const GENDER                 = [
        'UNDEFINED' => 0x0,
        'MAN'       => 0x1,
        'WOMAN'     => 0x2,
    ];
    public const GENDER_ZH              = [
        self::GENDER['UNDEFINED'] => '未知',
        self::GENDER['MAN']       => '男',
        self::GENDER['WOMAN']     => '女',
    ];
    public const NOT_HAVE_AVATAR        = 0;
    public const APP_SESSION_EXPIRE_DAY = 0xf;


    /**
     * 判断用户是否为root用户
     * @return bool
     */
    public function isRootUser(): bool
    {
        return $this->id = static::SUPER_USER_ID;
    }

    /**
     * 检查唯一字段是否存在 如用户名 手机号 email
     * @param string $name
     * @param string $field
     * @return bool
     */
    public static function checkUniqueFieldExist(string $name, string $field): bool
    {
        return self::getCount([$name => $field]) > 0;
    }

    /**
     * 管理员用户信息
     * @return \think\model\relation\HasOne
     */
    public function adminInfo()
    {
        return $this->hasOne(UserAdmin::class, 'uid');
    }

    /** 地址信息
     * @return \think\model\relation\MorphOne
     */
    public function address()
    {
        return $this->morphOne(Address::class, 'addressable');
    }

    /**
     * 头像
     * @return \think\model\relation\HasOne
     */
    public function avatar()
    {
        return $this->hasOne(Picture::class, 'id', 'avatar_pic_id');
    }

    /**
     * 用户的app推送信息.
     *
     * @return HasOne
     */
    public function appPush(): HasOne
    {
        return $this->hasOne(AppPush::class, 'uid', 'id');
    }

    /**
     * 判断用户对于的app state是否在前台
     * @return bool
     */
    public function appStateIsActive(): bool
    {
        if ( !$this->appStateStatus ) {
            return false;
        }
        return $this->appStateStatus->status !== AppStateStatus::STATUS['background'];
    }

    /**
     * 用户的app状态 前台还是后台
     *
     * @return HasOne
     */
    public function appStateStatus(): HasOne
    {
        return $this->hasOne(AppStateStatus::class, 'uid', 'id');
    }

    /**
     * 用户微信信息
     * @return \think\model\relation\HasOne
     */
    public function wxInfo()
    {
        return $this->hasOne(UserWxInfo::class, 'uid', 'id');
    }

    /**
     * 用户实名认证 信息
     * @return \think\model\relation\HasOne
     */
    public function idCardRealNameInfo()
    {
        return $this->hasOne(UserIdCardRealName::class, 'uid');
    }

    /**
     * 判断是否是管理员用户
     * @return bool
     */
    public function isAdminUser(): bool
    {
        return $this->adminInfo !== null;
    }

    /**
     * 判断是否为普通用户
     * @return bool
     */
    public function isCommonUser(): bool
    {
        return $this->adminInfo === null;
    }

    public function getUid(): int
    {
        return (int)$this->id;
    }

    public function checkAppIsLogin(string $session): bool
    {
        $isLogin = false;
        try {
            $appSessionDetail = AppSession::getDetail([
                ['session', '=', $session],
            ]);
            if ( date('Y-m-d H:i:s') < $appSessionDetail['expire'] ) {
                $isLogin = true;
                AppSession::updateItem(['session' => $session], [
                    'expire' => date("Y-m-d H:i:s", strtotime("+" .
                        self::APP_SESSION_EXPIRE_DAY . " days")),
                ]);
            }
        } catch (NotFoundException $ex) {

        } catch (Exception $ex) {
            BuffLog::sqlException($ex);
        }
        return $isLogin;
    }

    /**
     * 检查手机号码是否已注册
     * @param string $phone
     * @return bool
     */
    public function checkPhoneIsRegister(string $phone): bool
    {
        return self::getCount(['phone' => $phone]) > 0;
    }

    public static function createWxUser(array $user)
    {
        static::startTrans();
        UserWxInfo::startTrans();
        [$insertSuccess, $insertUserId] = static::addItem([
            'gender' => self::GENDER['UNDEFINED'],
        ], self::NEED_INSERT_ID);
        if ( !$insertSuccess ) {
            throw new Exception("添加用户失败");
        }
        $addData = [
            'uid'    => $insertUserId,
            'openid' => $user['openid'],
        ];
        if ( array_key_exists('nickname', $user) ) {
            $addData['nickname'] = $user['nickname'];
        }
        if ( array_key_exists('sex', $user) ) {
            $addData['sex'] = $user['sex'];
        }
        if ( array_key_exists('language', $user) ) {
            $addData['language'] = $user['language'];
        }
        if ( array_key_exists('city', $user) ) {
            $addData['city'] = $user['city'];
        }
        if ( array_key_exists('province', $user) ) {
            $addData['province'] = $user['province'];
        }
        if ( array_key_exists('country', $user) ) {
            $addData['country'] = $user['country'];
        }
        if ( array_key_exists('headimgurl', $user) ) {
            $addData['head_img_url'] = $user['headimgurl'];
        }
        if ( array_key_exists('subscribe_time', $user) ) {
            $addData['subscribe_time'] = date('Y-m-d H:i:s', $user['subscribe_time']);
        }
        if ( array_key_exists('unionid', $user) ) {
            $addData['unionid'] = $user['unionid'];
        }
        if ( array_key_exists('remark', $user) ) {
            $addData['remark'] = $user['remark'];
        }
        if ( array_key_exists('groupid', $user) ) {
            $addData['groupid'] = $user['groupid'];
        }
        if ( array_key_exists('tagid_list', $user) ) {
            $addData['tagid_list'] = $user['tagid_list'];
        }
        if ( array_key_exists('subscribe_scene', $user) ) {
            $addData['subscribe_scene'] = $user['subscribe_scene'];
        }
        if ( array_key_exists('qr_scene', $user) ) {
            $addData['qr_scene'] = $user['qr_scene'];
        }
        if ( array_key_exists('qr_scene_str', $user) ) {
            $addData['qr_scene_str'] = $user['qr_scene_str'];
        }
        if ( !UserWxInfo::addItem($addData) ) {
            static::rollback();
            UserWxInfo::rollback();
            throw new Exception('添加用户微信信息失败');
        }
        static::commit();
        UserWxInfo::commit();
    }
}
