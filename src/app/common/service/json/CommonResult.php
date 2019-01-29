<?php
/**
 * Created by IntelliJ IDEA.
 * User: buff
 * Date: 2019/1/2
 * Time: 16:29
 */

namespace bdk\app\common\service\json;

use bdk\constant\JsonReturnCode;
use JsonSerializable;

class CommonResult implements JsonSerializable
{
    /**
     * @var bool 是否成功
     */
    private $isSuccess = false;
    /**
     * @var int 错误代码
     */
    private $errCode = JsonReturnCode::DEFAULT_ERROR;
    /**
     * @var string 错误信息
     */
    private $errMsg = '';

    /**
     * @return bool
     */
    public function isSuccess(): bool
    {
        return $this->isSuccess;
    }

    /**
     * @param bool $isSuccess
     */
    public function setIsSuccess(bool $isSuccess): void
    {
        $this->isSuccess = $isSuccess;
    }

    /**
     * @return string|null
     */
    public function getErrMsg(): ?string
    {
        return $this->errMsg;
    }

    /**
     * @param string|null $errMsg
     */
    public function setErrMsg(?string $errMsg): void
    {
        $this->errMsg = $errMsg;
    }

    /**
     * @return int|null
     */
    public function getErrCode(): ?int
    {
        return $this->errCode;
    }

    /**
     * @param int|null $errCode
     */
    public function setErrCode(?int $errCode): void
    {
        $this->errCode = $errCode;
    }

    public function __construct($jsonObj = null)
    {
        if (is_array($jsonObj)) {
            $jsonObj = (object)$jsonObj;
        }
        if (empty($jsonObj)) {
            return;
        }
        $this->setIsSuccess($jsonObj->is_success ?? false);
        $this->setErrMsg($jsonObj->err_msg ?? '');
        $this->setErrCode($jsonObj->err_code ?? JsonReturnCode::DEFAULT_ERROR);

    }

    public function jsonSerialize()
    {
        return [
            'is_success' => $this->isSuccess(),
            'err_code'   => $this->getErrCode(),
            'err_msg'    => $this->getErrMsg(),
        ];
    }
}