<?php
/**
 * Created by IntelliJ IDEA.
 * User: buff
 * Date: 2019/1/2
 * Time: 11:01
 */

namespace bdk\app\common\model\json;

use JsonSerializable;

class Picture implements JsonSerializable
{
    /** 图片名
     * @var string
     */
    private $name;
    /**
     * 图片保存在服务器的物理路径 /var/www/xxx/public/uploads/2019/xx/xx.jpg
     * @var string
     */
    private $path;

    /**
     * 图片url路径 不包含域名 eg. /uploads/xx.jpg
     * @var string
     */
    private $url;
    /**
     * 缩略图物理路径 /var/www/xxx/public/uploads/thumb/2019/xx/xx.jpg
     * @var string
     */
    private $thumbPath;
    /**
     * 缩略图url路径 /uploads/thumb/2019/xx/xx.jpg
     * @var string
     */
    private $thumbUrl;

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string|null $name
     */
    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string|null
     */
    public function getPath(): ?string
    {
        return $this->path;
    }

    /**
     * @param string|null $path
     */
    public function setPath(?string $path): void
    {
        $this->path = $path;
    }

    /**
     * @return string|null
     */
    public function getUrl(): ?string
    {
        return $this->url;
    }

    /**
     * @param string|null $url
     */
    public function setUrl(?string $url): void
    {
        $this->url = $url;
    }

    /**
     * @return string|null
     */
    public function getThumbPath(): ?string
    {
        return $this->thumbPath;
    }

    /**
     * @param string|null $thumbPath
     */
    public function setThumbPath(?string $thumbPath): void
    {
        $this->thumbPath = $thumbPath;
    }

    /**
     * @return string|null
     */
    public function getThumbUrl(): ?string
    {
        return $this->thumbUrl;
    }

    /**
     * @param string|null $thumbUrl
     */
    public function setThumbUrl(?string $thumbUrl): void
    {
        $this->thumbUrl = $thumbUrl;
    }


    public function __construct($jsonObj = null)
    {
        if (is_array($jsonObj)) {
            $jsonObj = (object)$jsonObj;
        }
        if (empty($jsonObj)) {
            return;
        }
        $this->setName($jsonObj->name ?? null);
        $this->setPath($jsonObj->path ?? null);
        $this->setUrl($jsonObj->url ?? null);
        $this->setThumbPath($jsonObj->thumb_path ?? null);
        $this->setThumbUrl($jsonObj->thumb_url ?? null);

    }

    public function jsonSerialize()
    {
        return [
            'name'       => $this->name,
            'path'       => $this->path,
            'url'        => $this->url,
            'thumb_path' => $this->thumbPath,
            'thumb_url'  => $this->thumbUrl,
        ];
    }

}
