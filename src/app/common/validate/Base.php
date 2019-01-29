<?php
/**
 * Created by IntelliJ IDEA.
 * User: buff
 * Date: 2019/1/8
 * Time: 17:29
 */

namespace bdk\app\common\validate;

use bdk\app\common\model\Picture as PictureModel;
use think\Validate;
use think\facade\Request;
class Base extends Validate
{
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
        $domain = Request::domain();
        if ( strpos($picUrl, $domain) ) {
            $picUrl = str_replace($domain, '', $picUrl);
        }
        return PictureModel::getCount(['url' => $picUrl]) > 0;
    }

}