<?php
/**
 * Created by IntelliJ IDEA.
 * User: buff
 * Date: 2018/12/29
 * Time: 17:52.
 */

namespace bdk\app\common\model;

use Exception;
use InvalidArgumentException;

/**
 * 城市
 * Class City
 * @package bdk\app\common\model
 */
class City extends Base
{
    protected $field = [
        'id', 'ctime', 'utime', 'dtime',
        'cid', 'parentId', 'cityId', 'provinceId', 'areaName',
        'simpleName', 'lon', 'lat', 'areaCode', 'remark', 'prePinYin',
        'pinYin', 'level', 'simplePy', 'zipCode', 'countyId', 'wholeName',
    ];
    protected $json  = [];
    /**
     * 没有父级 即省级区域
     */
    const NOT_HAVE_PARENT = 0x0;
    /**
     * 省级区域
     */
    const PROVINCE_LEVEL = 0x1;
    /**
     * 市级区域
     */
    const CITY_LEVEL = 0x2;
    /**
     * 县级区域
     */
    const COUNTRY_LEVEL = 0x3;
    /**
     * 街道级区域
     */
    const STREET_LEVEL = 0x4;

    public function getProvince(array $field = []): array
    {
        $res      = $this->field($field)
            ->where('level', '=', self::PROVINCE_LEVEL)
            ->select();
        $province = [];
        foreach ($res as $v) {
            $province[] = $v->toArray();
        }
        return $province;
    }

    public function getSubCity(int $provinceId, array $field = []): array
    {
        $res   = $this->field($field)
            ->where('level', '=', self::CITY_LEVEL)
            ->where('parentId', '=', $provinceId)
            ->select();
        $citys = [];
        foreach ($res as $v) {
            $citys[] = $v->toArray();
        }
        return $citys;
    }

    public function getSubCounty(int $cityId, array $field = []): array
    {
        $res   = $this->field($field)
            ->where('level', '=', self::COUNTRY_LEVEL)
            ->where('parentId', '=', $cityId)
            ->select();
        $citys = [];
        foreach ($res as $v) {
            $citys[] = $v->toArray();
        }
        return $citys;
    }

    public function checkExist(string $cityId): bool
    {
        return is_int($this->where('cid', $cityId)->value('id'));
    }

    /**
     * 检查城市id是否存在
     * @param string $cityId
     * @return bool
     */
    public function checkCityExist(string $cityId): bool
    {
        return $this->where([
                ['cid', '=', $cityId],
                ['level', '=', self::CITY_LEVEL],
            ])->count() == 1;
    }

    /**
     * 根据cid获取城市名称
     * @param string $cid
     * @return string
     */
    public static function getAreaNameByCid(string $cid): string
    {
        try {
            $areaName = self::getValue(['cid' => $cid], 'areaName');
        } catch (Exception $ex) {
            throw new InvalidArgumentException("城市cid不正确");
        }
        return $areaName;
    }
}
