<?php
/**
 * Created by IntelliJ IDEA.
 * User: buff
 * Date: 2019/1/2
 * Time: 16:09
 */

namespace bdk\app\common\service\json;

use JsonSerializable;

class RegisterConfig implements JsonSerializable
{
    public const REGISTER_TYPE    = [
        'ACCOUNT_REGISTER'    => 0x0,
        'PHONE_REGISTER'      => 0x1,
        'EMAIL_CODE_REGISTER' => 0x2,
    ];
    public const REGISTER_TYPE_ZH = [
        self::REGISTER_TYPE['ACCOUNT_REGISTER']    => '账号注册',
        self::REGISTER_TYPE['PHONE_REGISTER']      => '手机注册',
        self::REGISTER_TYPE['EMAIL_CODE_REGISTER'] => '邮箱注册',
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
     * @var string 手机号
     */
    private $phone;
    /**
     * @var string 邮箱
     */
    private $email;
    /**
     * @var int 注册方式
     */
    private $registerType;

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
     * @return int|null
     */
    public function getRegisterType(): ?int
    {
        return $this->registerType;
    }

    /**
     * @param int|null $registerType
     */
    public function setRegisterType(?int $registerType): void
    {
        $registerType       = in_array($registerType, self::REGISTER_TYPE, true)
            ? $registerType : null;
        $this->registerType = $registerType;
    }


    public function __construct($jsonObj = null)
    {
        if ( is_array($jsonObj) ) {
            $jsonObj = (object)$jsonObj;
        }
        if ( empty($jsonObj) ) {
            return;
        }
        $this->setRegisterType($jsonObj->register_type ?? null);
        $this->setAccount($jsonObj->account ?? null);
        $this->setPwd($jsonObj->pwd ?? null);
        $this->setEmail($jsonObj->email ?? null);
        $this->setPhone($jsonObj->phone ?? null);

    }

    public function jsonSerialize()
    {
        return [
            'register_type' => $this->getRegisterType(),
            'account'       => $this->getAccount(),
            'pwd'           => $this->getPwd(),
            'email'         => $this->getEmail(),
            'phone'         => $this->getPhone(),
        ];
    }

}
