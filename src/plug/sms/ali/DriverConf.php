<?php

/*
 * Author: buff <admin@buffge.com>
 * Created on : 2018-12-19, 15:27:08
 * QQ:1515888956
 */
namespace bdk\plug\sms\ali;

use JsonSerializable;

class DriverConf implements JsonSerializable
{
    public function jsonSerialize()
    {
        return [
            'accessKey'    => $this->accessKey,
            'accessSecret' => $this->accessSecret,
            'sign'         => $this->sign,
            'template'     => $this->template,
        ];
    }
    private $accessKey;
    private $accessSecret;
    private $sign;
    private $template;

    public function __construct($jsonObj = null)
    {
        if (is_array($jsonObj)) {
            $jsonObj = (object) $jsonObj;
        }
        if (empty($jsonObj)) {
            return;
        }
        $this->setProvinceCid($jsonObj->province ?? null);
    }
    public function getAccessKey(): ?string
    {
        return $this->accessKey;
    }
    public function getAccessSecret(): ?string
    {
        return $this->accessSecret;
    }
    public function getSign(): ?string
    {
        return $this->sign;
    }
    public function getTemplate(): ?array
    {
        return $this->template;
    }
    public function setAccessKey(?string $accessKey)
    {
        $this->accessKey = $accessKey;
    }
    public function setAccessSecret(?string $accessSecret)
    {
        $this->accessSecret = $accessSecret;
    }
    public function setSign(?string $sign)
    {
        $this->sign = $sign;
    }
    public function setTemplate(?array $template)
    {
        $this->template = $template;
    }
}