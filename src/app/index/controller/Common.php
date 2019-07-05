<?php

namespace bdk\app\index\controller;

use bdk\app\common\controller\Base;
use bdk\app\common\model\City as CityModel;
use bdk\app\common\model\Picture as PictureModel;
use bdk\constant\JsonReturnCode;
use think\captcha\Captcha;
use think\facade\{Cache, Cookie, Request};
use think\facade\App;

class Common extends Base
{
    /**
     * 上传图片
     * @route /uploadImg
     */
    public function uploadImg()
    {
        $name = Request::post('name');
        $file = Request::file($name);
//        return json([
//            'code' => -1,
//            'data' => [
//                'file' => $file,
//                'post' => Request::post(),
//                'ext'=>$file->getInfo(),
//            ],
//        ]);
        $savePath = '/uploads/';
        $json     = ['code' => JsonReturnCode::SUCCESS,];
        if ( $file ) {
            $info = $file->validate(['size' => 1024 * 1024 * 8, 'ext' => 'jpg,jpeg,png,gif'])
                         ->move(App::getRootPath() . '/public' . $savePath);
            if ( $info ) {
                $logoUrl      = $savePath . $info->getSaveName();
                $json['data'] = [
                    'name' => $info->getInfo()['name'],
                    'url'  => $logoUrl,
                ];
                $fullPath     = App::getRootPath() . '/public' . $savePath . '/' . $info->getSaveName();
                [$isInsertSuccess, $picId] = PictureModel::addItem([
                    'title' => $json['data']['name'] ?? '',
                    'path'  => '/public' . $savePath . $info->getSaveName(),
                    'url'   => $logoUrl,
                    'size'  => filesize($fullPath),
                ], PictureModel::NEED_INSERT_ID);
                if ( !$isInsertSuccess ) {
                    $json['code'] = JsonReturnCode::SERVER_ERROR;
                    unset($json['data']);
                    $json['msg'] = '插入数据库失败';
                    unlink($fullPath);
                } else {
                    $json['data']['picId'] = $picId;
                }
            } else {
                $json['code'] = JsonReturnCode::DEFAULT_ERROR;
                $json['msg']  = $file->getError();
            }
        } else {
            $json['code'] = JsonReturnCode::DEFAULT_ERROR;
            $json['msg']  = "未能获取到图片";
        }
        if ( $json['code'] !== JsonReturnCode::SUCCESS ) {
            $json['error'] = $json['msg'];
        }
        return json($json);
    }

    /**
     * 获取验证码
     * @route /getVerifyCode
     */
    public function getVerifyCode()
    {
        $config  = [
            'fontSize' => 22, // 验证码字体大小(px)
            'useCurve' => false, // 是否画混淆曲线
            'useNoise' => false, // 是否添加杂点
            'imageH'   => 44, // 验证码图片高度
            'imageW'   => 186, // 验证码图片宽度
            'length'   => 4, // 验证码位数
        ];
        $captcha = new Captcha($config);
        return $captcha->entry();
    }

    public function getRegion()
    {
        $json = [
            'code' => JsonReturnCode::SUCCESS,
            'data' => [
                'region' => [],
            ],
        ];
        if ( Cache::has('region') ) {
            $json['data']['region'] = Cache::get('region');
            return json($json);
        }
        $region       = [];
        $provinceList = $this->getProvinceList();
        foreach ($provinceList as $provinceItem) {
            $item     = [
                'cid'      => $provinceItem['cid'],
                'areaName' => $provinceItem['areaName'],
                'level'    => 1,
                'children' => [],
            ];
            $cityList = $this->getSubCity($provinceItem['cid']);
            foreach ($cityList as $cityItem) {
                $item2      = [
                    'cid'      => $cityItem['cid'],
                    'areaName' => $cityItem['areaName'],
                    'level'    => 2,
                    'children' => [],
                ];
                $countyList = $this->getSubCounty($cityItem['cid']);
                foreach ($countyList as $countyItem) {
                    $item3               = [
                        'cid'      => $countyItem['cid'],
                        'areaName' => $countyItem['areaName'],
                        'level'    => 3,
                        'children' => [],
                    ];
                    $item2['children'][] = $item3;
                }
                $item['children'][] = $item2;
            }
            $region[] = $item;
        }
        Cache::set('region', $region);
        $json['data']['region'] = $region;
        return json($json);
    }

    public function getAllProvince()
    {
        $cityModel                     = CityModel::regInstance();
        $json                          = [
            'code' => JsonReturnCode::SUCCESS,
            'data' => [
                'province_list' => [],
            ],
        ];
        $json['data']['province_list'] = $cityModel->getProvince();
        return json($json);
    }

    private function getProvinceList()
    {
        $cityModel = CityModel::regInstance();
        return $cityModel->getProvince();
    }

    public function getCitysByProvinceCid()
    {
        $provinceCid               = Request::get('cid');
        $json                      = [
            'code' => JsonReturnCode::SUCCESS,
            'data' => [
                'city_list' => [],
            ],
        ];
        $json['data']['city_list'] = $this->getSubCity($provinceCid);
        return json($json);
    }

    public function getCountysByCityCid()
    {
        $cityCid                     = Request::get('cid');
        $json                        = [
            'code' => JsonReturnCode::SUCCESS,
            'data' => [
                'county_list' => [],
            ],
        ];
        $json['data']['county_list'] = $this->getSubCounty($cityCid);
        return json($json);
    }

    private function getSubCity(int $provinceId)
    {
        $cityModel = CityModel::regInstance();
        $city      = $cityModel->getSubCity($provinceId);
        return $city;
    }

    private function getSubCounty(int $cityId)
    {
        $cityModel = CityModel::regInstance();
        $county    = $cityModel->getSubCounty($cityId);
        return $county;
    }

    public function exportConstant()
    {
        $model     = Request::get('m');
        $className = "\\app\\common\\model\\{$model}";
        if ( !class_exists($className) ) {
            $className = "\\bdk" . $className;
        }
        $class = new $className;
        return json($class::exportConstant());
    }

    /**
     * @route /getSessionId
     * @return \think\response\Json
     */
    public function getSessionId()
    {
        $sessionId = '';
        if ( Cookie::has('PHPSESSID') ) {
            $sessionId = Cookie::get('PHPSESSID');
        }
        $json = [
            'code' => JsonReturnCode::SUCCESS,
            'data' => [
                'sessionId' => $sessionId,
            ],
        ];
        return json($json);
    }

    /**
     * 获取压缩图片
     * @route /getThumb get
     */
    public function getThumb()
    {
        $domain     = Request::domain();
        $publicPath = App::getRootPath() . '/public';
        $width      = Request::has('width') ? (int)Request::get('width') : 150;
        $path       = Request::get('path');
        if ( 0 === strpos($path, $domain) ) {
            $path = str_replace($domain, '', $path);
        }
        $info = @getimagesize($publicPath . '/' . $path);
        if ( false === $info ) {
            header('Content-type:image/jpeg');
            $ch = curl_init($path);
            echo curl_exec($ch);
            die;
            return json([
                'code' => JsonReturnCode::SERVER_ERROR,
                'msg'  => '图片不正确',
            ]);
        }
        $imgInfo = [
            'width'  => $info[0],
            'height' => $info[1],
            'type'   => image_type_to_extension($info[2], false),
            'mime'   => $info['mime'],
        ];
        $height  = $width / $info[0] * $info[1];
        $fun     = "imagecreatefrom{$imgInfo['type']}";
        $im      = @$fun($publicPath . '/' . $path);
        $img     = imagecreatetruecolor($width, $height);
        $color   = imagecolorallocate($img, 255, 255, 255);
        imagefill($img, 0, 0, $color);
        imagecopyresampled($img, $im, 0, 0, 0, 0, $width, $height, $info[0], $info[1]);
        header('Content-type:image/jpeg');
        imagejpeg($img);
        imagedestroy($img);
        die;
    }

}
