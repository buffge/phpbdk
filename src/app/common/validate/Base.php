<?php
/**
 * Created by IntelliJ IDEA.
 * User: buff
 * Date: 2019/1/8
 * Time: 17:29
 */

namespace bdk\app\common\validate;

use bdk\app\common\model\Picture as PictureModel;
use bdk\app\common\model\City as CityModel;
use bdk\app\common\model\UserAddress as UserAddressModel;
use bdk\traits\Register;
use bdk\utils\Common as Bdk;
use think\facade\Request;
use think\Validate;

class Base extends Validate
{
    use Register;

    public function __construct(array $rules = [], array $message = [], array $field = [])
    {
        parent::__construct($rules, $message, $field);
        $this->regex['phone'] = '^1[3456789]\d{9}$';
    }

    public function isJson($val): bool
    {
        return is_string($val) && !is_null(json_decode($val));
    }

    /**
     * 验证图片是否存在
     * @param $picUrl
     * @return bool
     */
    public function validPic($picUrl): bool
    {
        if ( is_int($picUrl) ) {
            return PictureModel::getCount(['id' => $picUrl]) === 1;
        } elseif ( is_string($picUrl) ) {
            $domain = Request::domain();
            if ( strpos($picUrl, $domain) ) {
                $picUrl = str_replace($domain, '', $picUrl);
            }
            return PictureModel::getCount(['url' => $picUrl]) > 0;
        } else {
            return false;
        }

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
}