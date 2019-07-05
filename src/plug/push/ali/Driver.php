<?php

namespace bdk\plug\push\ali;

use AlibabaCloud\Client\AlibabaCloud;
use app\common\model\User as UserModel;
use bdk\app\common\model\AppPush;
use bdk\app\common\model\Log;
use Exception;
use think\facade\Config;

class Driver
{
    public static function push(int $uid, string $title, string $body)
    {
        $pushConfig = Config::pull('push');
        $pushDriver = $pushConfig['driver'];
        $driverConf = $pushConfig[$pushDriver];
        AlibabaCloud::accessKeyClient($driverConf['accessKeyId'], $driverConf['accessSecret'])
                    ->regionId($driverConf['regionId'])// replace regionId as you need
                    ->asDefaultClient();
        try {
            $user = UserModel::get($uid);
            if ( !$user->appPush ) {
                return;
            }
            $os         = $user->appPush->os;
            $deviceType = AppPush::OS['android'] === $os ? 'ANDROID' : 'iOS';
            $deviceId   = $user->appPush->device_id;
            $appKey     = $driverConf['appKey'][$deviceType];
            $query      = [
                'AppKey'      => $appKey,
                'Target'      => "DEVICE",
                'PushType'    => "NOTICE",
                'DeviceType'  => $deviceType,
                'TargetValue' => $deviceId,
                'Title'       => $title,
                'Body'        => $body,
            ];
            if ( $deviceType === "iOS" ) {
                $query['iOSApnsEnv']            = $driverConf['iosEnv'];
                $query['iOSBadgeAutoIncrement'] = 'True';
            } else {
                $query['AndroidNotifyType']          = 'BOTH';
                $query['AndroidNotificationChannel'] = '1';
            }
            $result = AlibabaCloud::rpc()
                                  ->product('Push')
                                  ->version('2016-08-01')
                                  ->action('Push')
                                  ->method('POST')
                                  ->options([
                                      'query' => $query,
                                  ])
                                  ->request();
            Log::debug(['query' => $query, 'res' => $result->toArray()], '阿里云推送结果');
        } catch (Exception $ex) {
            Log::sqlException($ex);
        }

    }
}