<?php
/**
 * Created by IntelliJ IDEA.
 * User: buff
 * Date: 2019/1/3
 * Time: 10:57
 */


namespace bdk\app\common\model\json;

use bdk\app\common\model\City as CityModel;
use JsonSerializable;

/**
 * 地址
 * Class Address
 * @package app\common\model\json
 */
class Address implements JsonSerializable
{
    /**
     * 省cid
     * @var string
     */
    private $provinceCid;
    /**
     * 市cid
     * @var string
     */
    private $cityCid;
    /**
     * 县cid
     * @var string
     */
    private $countyCid;
    /**
     * 详细地址
     * @var string
     */
    private $detail;
    /**
     * 完整地址
     * @var string
     */
    private $whole;

    public function __construct($jsonObj = null)
    {
        if (is_array($jsonObj)) {
            $jsonObj = (object)$jsonObj;
        }
        if (empty($jsonObj)) {
            return;
        }
        $this->setProvinceCid($jsonObj->province ?? null);
        $this->setCityCid($jsonObj->city ?? null);
        $this->setCountyCid($jsonObj->county ?? null);
        $this->setDetail($jsonObj->detail ?? null);
        if (!property_exists($jsonObj, 'whole')) {
            $this->generateWhole();
        } else {
            $this->setWhole($jsonObj->whole);
        }
    }

    public function getProvinceCid(): ?string
    {
        return $this->provinceCid;
    }

    public function getCityCid(): ?string
    {
        return $this->cityCid;
    }

    public function getCountyCid(): ?string
    {
        return $this->countyCid;
    }

    public function getDetail(): ?string
    {
        return $this->detail;
    }

    public function getWhole(): ?string
    {
        return $this->whole;
    }

    public function setProvinceCid(?string $provinceCid): void
    {
        $this->provinceCid = $provinceCid;
    }

    public function setCityCid(?string $cityCid): void
    {
        $this->cityCid = $cityCid;
    }

    public function setCountyCid(?string $countyCid): void
    {
        $this->countyCid = $countyCid;
    }

    public function setDetail(?string $detail): void
    {
        $this->detail = $detail;
    }

    public function setWhole(?string $whole): void
    {
        $this->whole = $whole;
    }

    public function generateWhole(): void
    {
        if (empty($this->provinceCid) || empty($this->cityCid) || empty($this->cityCid)) {
            return;
        }
        $provinceName = CityModel::getAreaNameByCid($this->provinceCid);
        $cityName     = CityModel::getAreaNameByCid($this->cityCid);
        $countyName   = CityModel::getAreaNameByCid($this->countyCid);
        $detail       = $this->detail ?? '';
        $this->whole  = $provinceName . $cityName . $countyName . $detail;
    }

    public function jsonSerialize()
    {
        return [
            'province' => $this->provinceCid,
            'city'     => $this->cityCid,
            'county'   => $this->countyCid,
            'detail'   => $this->detail,
            'whole'    => $this->whole,
        ];
    }

}
