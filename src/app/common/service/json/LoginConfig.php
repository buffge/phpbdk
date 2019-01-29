<?php
/**
 * Created by IntelliJ IDEA.
 * User: buff
 * Date: 2019/1/2
 * Time: 16:09
 */

namespace bdk\app\common\service\json;

use JsonSerializable;

class LoginConfig implements JsonSerializable
{
    public const LOGIN_TYPE = [
        'ACCOUNT_LOGIN'           => 0x0,
        'PHONE_VERIFY_CODE_LOGIN' => 0x1,
        'EMAIL_VERIFY_CODE_LOGIN' => 0x2,
    ];
    /**
     * @var string 账号
     */
    private $account;
    /**
     * @var string 密码
     */
    private $pwd;
    /**
     * @var string 账号登录验证码
     */
    private $accountVerifyCode;
    /**
     * @var string 手机号
     */
    private $phone;
    /**
     * @var string 手机验证码
     */
    private $phoneVerifyCode;
    /**
     * @var string 邮箱
     */
    private $email;
    /**
     * @var string 邮箱验证码
     */
    private $emailVerifyCode;
    /**
     * @var int 登录方式
     */
    private $loginType;

    /**
     * @return int|null
     */
    public function getLoginType(): ?int
    {
        return $this->loginType;
    }

    /**
     * @param int|null $loginType
     */
    public function setLoginType(?int $loginType): void
    {
        $this->loginType = $loginType;
    }

    public function __construct($jsonObj = null)
    {
        if (is_array($jsonObj)) {
            $jsonObj = (object)$jsonObj;
        }
        if (empty($jsonObj)) {
            return;
        }
        $this->setAccount($jsonObj->account ?? null);
        $this->setPwd($jsonObj->pwd ?? null);
        $this->setAccountVerifyCode($jsonObj->account_verify_code ?? null);
        $this->setPhone($jsonObj->phone ?? null);
        $this->setPhoneVerifyCode($jsonObj->phone_verify_code ?? null);
        $this->setEmail($jsonObj->email ?? null);
        $this->setEmailVerifyCode($jsonObj->email_verify_code ?? null);
        $this->setLoginType($jsonObj->login_type ?? null);

    }

    public function jsonSerialize()
    {
        return [
            'account'             => $this->getAccount(),
            'pwd'                 => $this->getPwd(),
            'account_verify_code' => $this->getAccountVerifyCode(),
            'phone'               => $this->getPhone(),
            'phone_verify_code'   => $this->getPhoneVerifyCode(),
            'email'               => $this->getEmail(),
            'email_verify_code'   => $this->getEmailVerifyCode(),
            'login_type'          => $this->getLoginType(),
        ];
    }

    /**
     * @return string|null
     */
    public function getAccountVerifyCode(): ?string
    {
        return $this->accountVerifyCode;
    }

    /**
     * @param string|null $accountVerifyCode
     */
    public function setAccountVerifyCode(?string $accountVerifyCode): void
    {
        $this->accountVerifyCode = $accountVerifyCode;
    }

    /**
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @param string|null $email
     */
    public function setEmail(?string $email): void
    {
        $this->email = $email;
    }

    /**
     * @return string|null
     */
    public function getEmailVerifyCode(): ?string
    {
        return $this->emailVerifyCode;
    }

    /**
     * @param string|null $emailVerifyCode
     */
    public function setEmailVerifyCode(?string $emailVerifyCode): void
    {
        $this->emailVerifyCode = $emailVerifyCode;
    }

    /**
     * @return string|null
     */
    public function getAccount(): ?string
    {
        return $this->account;
    }

    /**
     * @param string|null $account
     */
    public function setAccount(?string $account): void
    {
        $this->account = $account;
    }

    /**
     * @return string|null
     */
    public function getPwd(): ?string
    {
        return $this->pwd;
    }

    /**
     * @param string|null $pwd
     */
    public function setPwd(?string $pwd): void
    {
        $this->pwd = $pwd;
    }

    /**
     * @return string|null
     */
    public function getPhone(): ?string
    {
        return $this->phone;
    }

    /**
     * @param string|null $phone
     */
    public function setPhone(?string $phone): void
    {
        $this->phone = $phone;
    }

    /**
     * @return string|null
     */
    public function getPhoneVerifyCode(): ?string
    {
        return $this->phoneVerifyCode;
    }

    /**
     * @param string|null $phoneVerifyCode
     */
    public function setPhoneVerifyCode(?string $phoneVerifyCode): void
    {
        $this->phoneVerifyCode = $phoneVerifyCode;
    }

}