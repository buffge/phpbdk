<?php

/*
 * Author: buff <admin@buffge.com>
 * Created on : 2018-12-16, 23:21:51
 * QQ:1515888956
 */
namespace bdk\plug\oss;

use think\facade\Config as TpConf;
use bdk\traits\Register;
use OSS\OssClient;
use OSS\Core\OssException;

/**
 * 上传文件
 * 搜索文件
 * 查看或下载文件
 * 删除文件或文件夹
 * 控制OSS资源的访问权限
 * 记录OSS资源的访问信息
 * 防止OSS上的数据被其他人盗链
 * 使用自定义域名访问OSS资源
 * 跨域资源共享
 * 在指定时间自动批量删除文件
 * 将一个存储空间的数据跨区域复制到另一个存储空间
 * 获取源数据内容
 * 修改 HTTP头
 * 查看资源使用情况
 * 处理OSS中存储的图片
 *
 */
class Driver
{
    use Register;
    private $accessKeyId;
    private $accessKeySecret;
    private $endpoint;
    private $bucket;
    private $defaultOssClient;

    public function __construct()
    {
        $ossConf               = TpConf::pull('oss');
        $this->accessKeyId     = $ossConf['accessKeyId'];
        $this->accessKeySecret = $ossConf['accessKeySecret'];
        $this->endpoint        = $ossConf['endpoint'];
        $this->bucket          = $ossConf['bucket'];
    }

    public function createBucket(string $endpoint, string $bucket)
    {
        try {
            $ossClient = new OssClient($this->accessKeyId, $this->accessKeySecret, $endpoint);
            $ossClient->createBucket($bucket);
        } catch (OssException $e) {
            print $e->getMessage();
        }
    }

    public function getDefaultOssClient(): OssClient
    {
        return $this->defaultOssClient ?? new OssClient($this->accessKeyId, $this->accessKeySecret, $this->endpoint);
    }

    /**
     * 下载文件
     * @alias downloadFile
     * @param string $object
     * @param string $bucket
     * @return string
     */
    public function file(string $object, string $bucket = null): string
    {
        return $this->downloadFile($object, $bucket);
    }

    /**
     * 下载文件
     * @param string $object
     * @param string $bucket
     * @return string
     */
    public function downloadFile(string $object, string $bucket = null): string
    {
        $ossClient = $this->getDefaultOssClient();
        $content   = $ossClient->getObject($bucket ?? $this->bucket, $object);
        return $content;
    }

    /**
     * 判断文件是否存在
     * @alias fileExist
     * @param string $object
     * @param string $bucket
     * @return bool
     */
    public function isFile(string $object, string $bucket = null): bool
    {
        return $this->fileExist($object, $bucket);
    }

    /**
     * 判断文件是否存在
     * @param string $object
     * @param string $bucket
     * @return bool
     */
    public function fileExist(string $object, string $bucket = null): bool
    {
        $ossClient = $this->getDefaultOssClient();
        return $ossClient->doesObjectExist($bucket ?? $this->bucket, $object);
    }

    /**
     * 上传文件
     * @param string $content
     * @param string $bucket
     * @return bool
     */
    public function uploadFile(
        string $content,
        string $fileName,
        string $path = '',
                               array $options = [],
        string $bucket = null
    ): bool {
        $ossClient = $this->getDefaultOssClient();
        $object    = $path . $fileName;
        $res       = $ossClient->putObject($bucket ?? $this->bucket, $object, $content, $options);
        return is_bool($res) ? $res : false;
    }

    /**
     * 删除文件
     * @param string $object
     * @param string $bucket
     * @return bool
     */
    public function deleteFile(string $object, string $bucket = null): bool
    {
        $ossClient = $this->getDefaultOssClient();
        return $ossClient->doesObjectExist($bucket ?? $this->bucket, $object);
    }
}
