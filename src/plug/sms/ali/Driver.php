<?php
/**
 * Created by IntelliJ IDEA.
 * User: buff
 * Date: 2019/3/12
 * Time: 13:44
 */

namespace bdk\plug\sms\ali;

use AlibabaCloud\Client\AlibabaCloud;
use AlibabaCloud\Client\Exception\ClientException;
use AlibabaCloud\Client\Exception\ServerException;
use bdk\app\common\model\AliSmsRecord as AliSmsRecordModel;
use bdk\Common as Bdk;
use bdk\exception\SendSmsException;
use Exception;

class Driver
{
    private $accessKeyId;
    private $accessSecret;
    private $sign;
    private $template;
    private $regionId;
    private const DEFAULT_REGION_ID = 'cn-hangzhou';

    public function __construct(array $conf)
    {
        $this->accessKeyId  = $conf['accessKeyId'];
        $this->accessSecret = $conf['accessSecret'];
        $this->sign         = $conf['sign'];
        $this->template     = $conf['template'];
        $this->regionId     = $conf['regionId'] ?? self::DEFAULT_REGION_ID;
        AlibabaCloud::accessKeyClient($this->accessKeyId, $this->accessSecret)
            ->regionId($this->regionId)
            ->asGlobalClient();
    }

    public function sendSms(string $phone, string $templateCode, array $param): array
    {
        if ( !Bdk::isMobilePhone($phone) ) {
            throw new Exception("{$phone}不是有效的手机号码");
        }
        $errMsg = '';
        if ( !$this->judgeMsg($templateCode, $param, $errMsg) ) {
            throw new Exception($errMsg);
        }

        try {
            $query  = [
                'PhoneNumbers'  => $phone,
                'SignName'      => $this->sign,
                'TemplateCode'  => $templateCode,
                'TemplateParam' => json_encode($param, JSON_UNESCAPED_UNICODE),
            ];
            $result = AlibabaCloud::rpcRequest()
                ->product('Dysmsapi')
                // ->scheme('https') // https | http
                ->version('2017-05-25')
                ->action('SendSms')
                ->method('POST')
                ->options([
                    'query' => $query,
                ])
                ->request();
            $res    = $result->toArray();
            if ( !AliSmsRecordModel::addItem([
                'phone_numbers'  => $query['PhoneNumbers'],
                'sign_name'      => $query['SignName'],
                'template_code'  => $query['TemplateCode'],
                'template_param' => $query['TemplateParam'],
                'result_msg'     => $res['Message'],
                'req_id'         => $res['RequestId'],
                'biz_id'         => $res['BizId'] ?? null,
                'result_code'    => $res['Code'],
            ]) ) {
                throw new SendSmsException('发送短信记录插入到数据库中失败');
            }
            return [$res['Code'] === 'OK' && $res['Message'] === 'OK', $res['Message']];
        } catch (ClientException $e) {
            throw new SendSmsException($e->getErrorMessage());
        } catch (ServerException $e) {
            throw new SendSmsException($e->getErrorMessage());
        }
    }

    /**
     * 判断短信消息格式是否正确
     * @param string $templateCode
     * @param array $param
     * @param string $errMsg
     * @return bool
     */
    public function judgeMsg(string $templateCode, array $param, string &$errMsg): bool
    {
        if ( is_null($this->template) ) {
            $errMsg = "系统配置错误 ,未找到阿里云短信模板配置";
            return false;
        }
        if ( !key_exists($templateCode, $this->template) ) {
            $errMsg = "模板代码{$templateCode}不存在";
            return false;
        }
        $data = $this->template[$templateCode];
        foreach ($data['param'] as $v) {
            if ( !key_exists($v, $param) ) {
                $errMsg = "模板参数数组缺少{$v}参数";
            }
            if ( mb_strlen($param[$v]) > 20 ) {
                $errMsg = "模板参数{$v}长度超过20";
            }
        }
        return empty($errMsg);
    }
}