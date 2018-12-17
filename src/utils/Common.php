<?php

/*
 * Author: buff <admin@buffge.com>
 * Created on : 2018-7-9, 16:28:24
 * QQ:1515888956
 */
namespace bdk\utils;

use think\facade\Env;

abstract class Common
{
    const NUMBER_MAP_CHINESE = [
        0  => '零',
        1  => '一',
        2  => '二',
        3  => '三',
        4  => '四',
        5  => '五',
        6  => '六',
        7  => '七',
        8  => '八',
        9  => '九',
        10 => '十',
    ];

    /**
     * 判断是否为正确的手机号码
     * @param string $number
     * @return bool
     */
    public static function isMobilePhone(string $number): bool
    {
        $regex = '~^1[356789]\d{9}$~';
        return preg_match($regex, $number) === 1;
    }

    /**
     * 判断是否为正确的电话号码
     * @param string $number
     * @return bool
     */
    public static function isTelePhone(string $number): bool
    {
        $regex = '~^(0\d{2,3})?\s?[1-9]{6,}$~';
        return preg_match($regex, $number) === 1;
    }

    public static function removeNotUtf8Chars(string $oriStr): string
    {
        $regex = '~((?:[\x00-\x7F]|[\xC0-\xDF][\x80-\xBF]|[\xE0-\xEF][\x80-\xBF]{2}|[\xF0-\xF7][\x80-\xBF]{3}){1,})|.~';
        return preg_replace($regex, '$1', $oriStr);
    }

    public static function getServerStatus(): array
    {
        $res    = shell_exec('top -n 1 -b|cat |sed -n \'1,4p\'');
        preg_match('~%Cpu\(s\):\s+(?<cpu>\d{1,3}\.\d{1,2})\s+us~', $res, $matchs);
        $cpu    = (float) $matchs['cpu'];
        preg_match('~KiB\sMem\s:\s+(?<total>\d+)\s+total,\s+(?<free>\d+)\s+free,\s+(?<used>\d+) used~', $res, $matchs);
        $memory = number_format($matchs['used'] / $matchs['total'], 2);
        return [
            'cpu'    => $cpu,
            'memory' => $memory,
        ];
    }

    /**
     * 判断是否为微信客户端
     * @return bool
     */
    public static function isWxClient(): bool
    {
        return key_exists('HTTP_USER_AGENT', $_SERVER) && strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false;
    }

    public static function buildConfTree(array $oriConf): array
    {
        $conf       = [];
        $idMapName  = [];
        $oriSurplus = count($oriConf);
        $isTop      = true;
        do {
            $oriSurplus = count($oriConf);
            foreach ($oriConf as $k => $v) {
                if ($isTop) {
                    if ($v['pid'] === 0) {
                        $conf[$v['name']]    = $v;
                        $idMapName[$v['id']] = [$v['name']];
                        unset($conf[$v['name']]['name']);
                        unset($oriConf[$k]);
                        continue;
                    }
                }
                if (key_exists($v['pid'], $idMapName)) {
                    $parConf = null;
                    for ($i = 0, $len = count($idMapName[$v['pid']]); $i < $len; $i++) {
                        if ($i === 0) {
                            $parConf = &$conf[$idMapName[$v['pid']][0]];
                        } else {
                            $parConf = &$parConf[$idMapName[$v['pid']][$i]];
                        }
                    }
                    $idMapName[$v['id']] = array_merge($idMapName[$v['pid']], [$v['name']]);
                    $parConf[$v['name']] = $v;
                    unset($parConf[$v['name']]['name']);
                    unset($oriConf[$k]);
                    unset($parConf);
                }
            }
            $currSurplus = count($oriConf);
            $isTop       = false;
        } while ($currSurplus < $oriSurplus);
        return $conf;
    }

    public static function isIdCardNo(string $oriIdCardNo): bool
    {
        $idCardNo  = strtoupper($oriIdCardNo);
        $regx      = "/(^\d{15}$)|(^\d{17}([0-9]|X)$)/";
        $arr_split = array();
        if (!preg_match($regx, $idCardNo)) {
            return false;
        }
        if (15 == strlen($idCardNo)) { //检查15位
            $regx      = "/^(\d{6})+(\d{2})+(\d{2})+(\d{2})+(\d{3})$/";
            @preg_match($regx, $idCardNo, $arr_split);
            //检查生日日期是否正确
            $dtm_birth = "19" . $arr_split[2] . '/' . $arr_split[3] . '/' . $arr_split[4];
            return strtotime($dtm_birth) ? true : false;
        } else {      //检查18位
            $regx      = "/^(\d{6})+(\d{4})+(\d{2})+(\d{2})+(\d{3})([0-9]|X)$/";
            @preg_match($regx, $idCardNo, $arr_split);
            $dtm_birth = $arr_split[2] . '/' . $arr_split[3] . '/' . $arr_split[4];
            if (!strtotime($dtm_birth)) { //检查生日日期是否正确
                return false;
            } else {
                //检验18位身份证的校验码是否正确。
                //校验位按照ISO 7064:1983.MOD 11-2的规定生成，X可以认为是数字10。
                $arr_int = array(7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2);
                $arr_ch  = array('1', '0', 'X', '9', '8', '7', '6', '5', '4', '3', '2');
                $sign    = 0;
                for ($i = 0; $i < 17; $i++) {
                    $b    = (int) $idCardNo{$i};
                    $w    = $arr_int[$i];
                    $sign += $b * $w;
                }
                $n       = $sign % 11;
                $val_num = $arr_ch[$n];
                return $val_num === substr($idCardNo, 17, 1) ? true : false;
            }
        }
    }

    public static function isIdCardName(string $idCardName): bool
    {
        return preg_match("~[\u{3400}-\u{9fff}\u{f900}-\u{faff}]{2,4}~", $idCardName) === 1;
    }

    /**
     * 格式化时间为小时分钟秒微秒
     * @param float $time
     * @return string
     */
    public static function formatMicrotime(float $time): string
    {
        $sec    = (int) $time;
        $micSec = number_format($time - $sec, 3) * 1000;
        $str    = '';
        if ($sec > 60) {
            if ($sec > 3600) {
                $hours   = intval($sec / 3600);
                $lessSec = $sec - $hours * 3600;
                if ($lessSec > 60) {
                    $min       = intval($lessSec / 60);
                    $formatSec = $lessSec - $min * 60;
                    $str       = "{$hours}小时 {$min}分 {$formatSec}秒 {$micSec}毫秒";
                } else {
                    $str = "{$hours}小时 {$lessSec}秒 {$micSec}毫秒";
                }
            } else {
                $min       = intval($sec / 60);
                $formatSec = $sec - $min * 60;
                $str       = "{$min}分{$formatSec}秒{$micSec}毫秒";
            }
        } else {
            $str = "{$sec}秒{$micSec}毫秒";
        }
        return $str;
    }

    public static function getEnvInfo(): array
    {
        return [
            '应用根目录'      => Env::get('root_path'),
            '应用目录'       => Env::get('app_path'),
            '框架目录'       => Env::get('think_path'),
            '配置目录'       => Env::get('config_path'),
            '扩展目录'       => Env::get('extend_path'),
            'composer目录' => Env::get('vendor_path'),
            '运行缓存目录'     => Env::get('runtime_path'),
            '路由目录'       => Env::get('route_path'),
            '当前模块目录'     => Env::get('module_path'),
        ];
    }
}
