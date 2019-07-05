<?php
/**
 * Created by IntelliJ IDEA.
 * User: buff
 * Date: 2018/12/29
 * Time: 17:52.
 */

namespace bdk\app\common\model;

use think\facade\Request;

/**
 * 图片
 * Class Picture
 * @package bdk\app\common\model
 */
class Picture extends Base
{
    protected $field = [
        'id', 'ctime', 'utime', 'dtime',
        'title', 'path', 'url', 'size',
        'pictureable_id', 'pictureable_type',
    ];

    /**
     * 获取图片对应的多态模型。
     */
    public function pictureable()
    {
        return $this->morphTo();
    }

    public static function getIdByUrl(string $picUrl): int
    {
        $domain = Request::domain();
        if ( strpos($picUrl, $domain) ) {
            $picUrl = str_replace($domain, '', $picUrl);
        }
        return self::getValue(['url' => $picUrl], 'id');
    }

    /**
     * 图片的缩略图
     * @return \think\model\relation\HasMany
     */
    public function thumbs()
    {
        return $this->hasMany(Thumb::class, 'picture_id');
    }

    public function getThumb(int $width, ?int $height = null): string
    {
        $height = $height ?? $width;
        $res    = $this->thumbs()->where([
            ['width', '=', $width],
            ['height', '=', $height],
        ])->select();
        if ( $res->isEmpty() ) {
            #todo 构造一个缩略图
        } else {
            return $res->getAttr('url');
        }
    }
}
