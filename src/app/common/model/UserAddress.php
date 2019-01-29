<?php
/**
 * Created by IntelliJ IDEA.
 * User: buff
 * Date: 2018/12/29
 * Time: 17:52.
 */

namespace bdk\app\common\model;

use bdk\app\common\model\json\Address;

/**
 * 用户地址
 * Class UserAddress
 * @package bdk\app\common\model
 */
class UserAddress extends Base
{
    protected $field = [
        'id', 'ctime', 'utime', 'dtime',
        'uid',
        'province_cid', 'city_cid', 'county_cid', 'detail', 'whole',
    ];
    protected $json  = [
    ];

    public static function addUserAddress(int $uid, Address $address): bool
    {
        return self::addItem([
            'uid'          => $uid,
            'province_cid' => $address->getProvinceCid(),
            'city_cid'     => $address->getCityCid(),
            'county_cid'   => $address->getCountyCid(),
            'detail'       => $address->getDetail(),
            'whole'        => $address->getWhole(),
        ]);
    }

    /**
     * 更新地址
     * @param Address $address
     * @return bool
     */
    public function updateAddress(Address $address): bool
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
    public function buildFormatAddress(): Address
    {
        return new Address($this->jsonSerialize());
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
