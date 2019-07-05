<?php
/**
 * Created by IntelliJ IDEA.
 * User: buff
 * Date: 2019/1/8
 * Time: 17:29
 */

namespace bdk\app\common\validate;

use bdk\app\common\model\City as CityModel;
use bdk\app\common\model\Picture as PictureModel;
use bdk\app\common\model\User as UserModel;
use bdk\traits\Register;
use bdk\utils\Common as Bdk;
use think\facade\Request;
use think\Validate;

class Base extends Validate
{
    use Register;

    public function __construct(array $rules = [], array $message = [],
                                array $field = [])
    {
        parent::__construct($rules, $message, $field);
        $this->regex['phone'] = '^1[3456789]\d{9}$';
    }

    public function isJson($val): bool
    {
        return is_string($val) && $val === 'null' || !is_null(json_decode($val));
    }

    /**
     * 验证图片是否存在
     * @param $picUrl
     * @return bool
     */
    public function validPic($picUrl): bool
    {
        if ( is_int($picUrl) ) {
            return $picUrl === 0 || PictureModel::getCount(['id' => $picUrl]) === 1;
        } elseif ( is_string($picUrl) ) {
            $domain = Request::domain();
            if ( strpos($picUrl, $domain) ) {
                $picUrl = str_replace($domain, '', $picUrl);
            }
            return PictureModel::getCount(['url' => $picUrl]) > 0;
        } elseif ( is_array($picUrl) ) {
            if ( is_int($picUrl['picId']) ) {
                return $picUrl['picId'] === 0 || PictureModel::getCount(['id' => $picUrl['picId']])
                    === 1;
            }
        }
        return false;
    }

    /**
     * @param string $picIdList
     * @param $rule
     * @param $data
     * @return bool
     */
    public function validPicIdList(array $picIdList, $rule, $data): bool
    {
        return PictureModel::getCount([['id', 'in', $picIdList]]) === count($picIdList);
    }

    /**
     * 验证城市cid是否存在
     * @param $cityCid
     * @return bool
     */
    public function validCityCid($cityCid): bool
    {
        return CityModel::getCount([
                ['cid', '=', $cityCid],
            ]) === 1;
    }

    /**
     * 验证省cid是否存在
     * @param $povinceCid
     * @return bool
     */
    public function validProvinceCid($povinceCid): bool
    {
        return CityModel::getCount([
                ['level', '=', CityModel::PROVINCE_LEVEL],
                ['cid', '=', $povinceCid],
            ]) === 1;
    }

    /**
     * 验证地址是否正确
     * @param $addressArr
     * @return bool
     */
    public function validAddressArr($addressArr): bool
    {
        if ( !is_array($addressArr) ) {
            return false;
        }
        if ( count($addressArr) !== 3 ) {
            return false;
        }
        [$provinceCid, $cityCid, $countyCid] = $addressArr;
        return CityModel::getCount([
                ['cid', '=', $countyCid],
                ['cityId', '=', $cityCid],
                ['provinceId', '=', $provinceCid],
            ]) === 1;

    }

    /**
     * 验证是否为正确的身份证号码
     * @param $idCardNo
     * @return bool
     */
    public function validIdCardNo($idCardNo): bool
    {
        return !is_string($idCardNo) ? false : Bdk::isIdCardNo($idCardNo);
    }

    /**
     * 验证性别数值是否正确
     * @param $gender
     * @return bool
     */
    public function validGender($gender): bool
    {
        return in_array($gender, UserModel::GENDER, true);
    }
}