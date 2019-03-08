<?php
/**
 * Created by IntelliJ IDEA.
 * User: buff
 * Date: 2019/1/3
 * Time: 15:05
 */

namespace bdk\app\common\validate;


use bdk\app\common\model\User as UserModel;
use bdk\app\common\service\{Cache, Session, User as UserService};
use bdk\exception\NotFoundException;
use bdk\utils\Common as Bdk;

class User extends Base
{
    public const SCENE = [
        'commonUserAccountLogin' => 'commonUserAccountLogin',
        'accountLogin'           => 'accountLogin',
        'emailLogin'             => 'emailLogin',
        'emailRegister'          => 'emailRegister',
        'phoneLogin'             => 'phoneLogin',
        'editInfo'               => 'editInfo',
        'modifyPwd'              => 'modifyPwd',
        'add'                    => 'add',
    ];
    protected $rule    = [
        'account'                 => 'require|validAccount:thinkphp|unique:user,account',
        'loginAccount'            => 'require',
        'commonUserLoginAccount'  => 'require|validCommonUserAccount:thinkphp',
        'pwd'                     => 'require|validPwd:thinkphp',
        'loginPwd'                => 'require',
        'rePwd'                   => 'require|confirm:pwd',
        'isAdmin'                 => 'require|bool',
        'accountVerifyCode'       => 'require|captcha',
        'email'                   => 'email|unique:user,email',
        //邮箱登录时提交的邮箱地址,为了保证发送验证码的邮箱和提交的一样
        'emailLoginEmail'         => 'require|validEmailLoginEmail:thinkphp',
        'emailLoginVerifyCode'    => 'require|validEmailLoginVerifyCode:thinkphp',
        'emailRegisterEmail'      => 'require|validEmailRegisterEmail:thinkphp',
        'emailRegisterVerifyCode' => 'require|validEmailRegisterVerifyCode:thinkphp',
        'loginPhone'              => 'require|validPhone:thinkphp|isLoginSendVerifyCodePhone:thinkphp',
        'loginPhoneVerifyCode'    => 'require|validLoginPhoneVerifyCode:thinkphp',
        'editNick'                => 'length:2,10,|chsDash|unique:user,nick',
        'editEmail'               => 'email|unique:user,email',
        'editPhone'               => 'phone|unique:user,phone',
        'profile'                 => 'max:500',
        'modifyOriPwd'            => 'require|validOriPwd:thinkphp',
        'modifyNewPwd'            => 'require|validPwd:thinkphp',
        'modifyRePwd'             => 'require|confirm:modifyNewPwd',
    ];
    protected $message = [
        'modifyOriPwd.require'     => '原密码必填',
        'modifyOriPwd.validOriPwd' => '原密码不正确',
        'modifyNewPwd.require'     => '新密码必填',
        'modifyNewPwd.validPwd'    => '密码不是有效的格式,必须为4-32位大小写字母及~@!#$^&*等可见字符',
        'modifyRePwd.require'      => '确认密码必填',
        'modifyRePwd.confirm'      => '两次输入密码不一致',

        'editNick.length'  => '昵称长度为2-10字符',
        'editNick.chsDash' => '昵称只能为汉字、字母、数字和下划线_及破折号-',
        'editNick.unique'  => '昵称已存在',

        'editEmail.email'  => '邮箱格式不正确',
        'editEmail.unique' => '邮箱已被注册',

        'editEmail.phone'  => '手机号码格式不正确',
        'editEmail.unique' => '手机号码已被注册',

        'profile.maxLength' => '个人简介最多500字',

        'account.require'                               => '用户名必填',
        'account.validAccount'                          => '用户名格式不正确,必须为2-32位大小写字母数字-_',
        'account.unique'                                => '用户名已存在',
        'loginAccount.require'                          => '用户名必填',
        'commonUserLoginAccount.require'                => '用户名必填',
        'commonUserLoginAccount.validCommonUserAccount' => '用户名或密码错误或者你没有登录权限',
        'pwd.require'                                   => '密码必填',
        'loginPwd.require'                              => '密码必填',
        'pwd.validPwd'                                  => '密码不是有效的格式,必须为4-32位大小写字母及~@!#$^&*等可见字符',
        'rePwd.require'                                 => '确认密码必填',
        'rePwd.confirm'                                 => '两次密码不一致',
        'accountVerifyCode.require'                     => '验证码必填',
        'accountVerifyCode.captcha'                     => '验证码不正确',

        'email.email'                                    => '邮箱格式不正确',
        'email.unique'                                   => '邮箱已被注册',
        'emailLoginEmail.require'                        => '邮箱必填',
        'emailLoginEmail.validEmailLoginEmail'           => '邮箱不是刚刚发送邮件的邮箱',
        'emailLoginVerifyCode.require'                   => '邮箱验证码必填',
        'emailLoginVerifyCode.validEmailLoginVerifyCode' => '邮箱验证码不正确',

        'emailRegisterEmail.require'                           => '邮箱必填',
        'emailRegisterEmail.validEmailRegisterEmail'           => '邮箱不是刚刚发送邮件的邮箱',
        'emailRegisterVerifyCode.require'                      => '邮箱验证码必填',
        'emailRegisterVerifyCode.validEmailRegisterVerifyCode' => '邮箱验证码不正确',

        'loginPhone.require'                             => '手机号码必填',
        'loginPhone.validPhone'                          => '手机号码格式不正确',
        'loginPhone.isLoginSendVerifyCodePhone'          => '手机号码不是发送验证码的手机',
        'loginPhoneVerifyCode.require'                   => '手机验证码必填',
        'loginPhoneVerifyCode.validLoginPhoneVerifyCode' => '手机验证码不正确',
    ];
    protected $scene   = [
        self::SCENE['commonUserAccountLogin'] => [
            'commonUserLoginAccount', 'loginPwd', 'accountVerifyCode',
        ],
        self::SCENE['accountLogin']           => [
            'loginAccount', 'loginPwd', 'accountVerifyCode',
        ],
        self::SCENE['emailLogin']             => [
            'emailLoginEmail', 'emailLoginVerifyCode',
        ],
        self::SCENE['emailRegister']          => [
            'account', 'pwd', 'rePwd', 'email', 'emailRegisterEmail',
            'emailRegisterVerifyCode',
        ],
        self::SCENE['phoneLogin']             => [
            'loginPhone', 'loginPhoneVerifyCode',
        ],
        self::SCENE['editInfo']               => [
            'editNick', 'editEmail', 'editPhone', 'profile',
        ],
        self::SCENE['modifyPwd']              => [
            'modifyOriPwd', 'modifyNewPwd', 'modifyRePwd',
        ],
        self::SCENE['add']                    => [
            'account', 'pwd', 'rePwd', 'nick', 'email', 'phone',
            'profile', 'isAdmin',
        ],
    ];

    /**
     * 验证用户名是否符合规范
     * @param $val
     * @return bool
     */
    public function validAccount($val): bool
    {
        return preg_match('~^[a-zA-Z0-9_\-]{2,32}$~',
                $val) === 1;
    }

    /**
     * 验证密码是否符合规范
     * @param $val
     * @return bool
     */
    public function validPwd($val): bool
    {
        return preg_match('~^[a-zA-Z0-9\~`!@#\$%\^&\*\(\)_\-\+=\{\}\[\]\\\|;:"\',<\.>\?\/]{4,32}$~',
                $val) === 1;
    }

    /**
     *  验证邮箱登录验证码是否正确
     * @param $val
     * @return bool
     */
    public function validEmailLoginVerifyCode($val): bool
    {
        $sessionService = Session::regInstance();
        return $sessionService->getEmailLoginSendVerifyCode() === $val;
    }

    /**
     * 验证邮箱登录时邮箱与发送验证码的邮箱是否一致
     * @param $val
     * @return bool
     */
    public function validEmailLoginEmail($val): bool
    {
        $sessionService = Session::regInstance();
        return $sessionService->getLoginSendVerifyCodeEmail() === $val;
    }

    /**
     *  验证邮箱注册验证码是否正确
     * @param $val
     * @return bool
     */
    public function validEmailRegisterVerifyCode($val): bool
    {
        $sessionService = Session::regInstance();
        return $sessionService->getEmailRegisterSendVerifyCode() === $val;
    }

    /**
     * 验证邮箱注册时邮箱与发送验证码的邮箱是否一致
     * @param $val
     * @return bool
     */
    public function validEmailRegisterEmail($val): bool
    {
        $sessionService = Session::regInstance();
        return $sessionService->getRegisterSendVerifyCodeEmail() === $val;
    }

    /**
     * 验证手机号码是否正确
     * @param $val
     * @return bool
     */
    public function validPhone($val): bool
    {
        return Bdk::isMobilePhone($val);
    }

    /**
     * 验证手机号码是否为登录时发送验证码的手机号
     * @param $val
     * @return bool
     */
    public function isLoginSendVerifyCodePhone($val): bool
    {
        $sessionService = Session::regInstance();
        return $sessionService->getLoginSendVerifyCodePhone() === $val;
    }

    /**
     * 验证手机登录验证码是否正确
     * @param $val
     * @return bool
     */
    public function validLoginPhoneVerifyCode($val): bool
    {
        $cacheService = Cache::regInstance();
        return $cacheService->getLoginPhoneVerifyCode() === $val;
    }

    public function validOriPwd($oriPwd): bool
    {
        $userService = new UserService;
        return $userService->validOriPwd($oriPwd);
    }

    public function validCommonUserAccount($account): bool
    {
        try {
            $user = UserModel::getDetail([['account|email|phone', '=', $account]]);
        } catch (NotFoundException $ex) {
            return false;
        }
        return $user->isCommonUser();
    }
}
