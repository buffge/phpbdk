<?php
/**
 * Created by IntelliJ IDEA.
 * User: buff
 * Date: 2018/12/29
 * Time: 17:52.
 */

namespace bdk\app\common\model;

use bdk\app\common\model\json\Address as AddressJson;

/**
 * 用户地址
 * Class UserAddress
 * @package bdk\app\common\model
 */
class Address extends Base
{
    protected $field = [
        'id', 'ctime', 'utime', 'dtime',
        'province_cid', 'city_cid', 'county_cid', 'detail', 'whole',
        'addressable_id', 'addressable_type',
    ];
    protected $json  = [
    ];

    /**
     * 获取头像对应的多态模型。
     */
    public function addressable()
    {
        return $this->morphTo();
    }
#todo 添加用户地址 改动
//    public static function addUserAddress(AddressJson $address): bool
//    {
//        return self::addItem([
//            'province_cid' => $address->getProvinceCid(),
//            'city_cid'     => $address->getCityCid(),
//            'county_cid'   => $address->getCountyCid(),
//            'detail'       => $address->getDetail(),
//            'whole'        => $address->getWhole(),
//        ]);
//    }

    /**
     * 生成完整地址并保存
     * @return bool
     */
    public function generateWholeAndSave(): bool
    {
        if ( empty($this->province_cid) || empty($this->city_cid) || empty($this->county_cid) ) {
            return false;
        }
        $provinceName = City::getAreaNameByCid($this->province_cid);
        $cityName     = City::getAreaNameByCid($this->city_cid);
        $countyName   = City::getAreaNameByCid($this->county_cid);
        $detail       = $this->detail ?? '';
        $this->whole  = $provinceName . $cityName . $countyName . $detail;
        return $this->save();
    }

    /**
     * 生成完整的地址信息
     * @param string $provinceCid
     * @param string $cityCid
     * @param string $coutyCid
     * @param string $detail
     * @return string
     */
    public static function generateWholeAddress(string $provinceCid, string $cityCid, string $coutyCid, string $detail)
    {
        $provinceName = City::getAreaNameByCid($provinceCid);
        $cityName     = City::getAreaNameByCid($cityCid);
        $countyName   = City::getAreaNameByCid($coutyCid);
        return $provinceName . $cityName . $countyName . $detail;
    }

    /**
     * 更新地址
     * @param Address $address
     * @return bool
     */
    public function updateAddress(AddressJson $address): bool
    {
        $this->province_cid = $address->getProvinceCid();
        $this->city_cid     = $address->getCityCid();
        $this->county_cid   = $address->getCountyCid();
        $this->detail       = $address->getDetail();
        $this->whole        = $address->getWhole();
        return $this->save();
    }

    /**
     * 生成一个格式化的地址
     * @return Address
     */
    public function buildFormatAddress(): AddressJson
    {
        return new AddressJson($this->jsonSerialize());
    }

    public function jsonSerialize()
    {
        return [
            'province' => $this->getAttr('province_cid'),
            'city'     => $this->getAttr('city_cid'),
            'county'   => $this->getAttr('county_cid'),
            'detail'   => $this->getAttr('detail'),
            'whole'    => $this->getAttr('whole'),
        ];
    }


}
