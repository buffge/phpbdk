<?php

/*
 * Author: buff <admin@buffge.com>
 * Created on : 2018-11-29, 23:56:06
 * QQ:1515888956
 */
namespace bdk\utils;

use think\facade\Env;
use bdk\traits\Register;
use bdk\traits\DisableInstantiation;

class Dir
{
    use Register;
    use DisableInstantiation;
    /**
     * 获取应用根目录
     * @return string
     */
    public function getRootDir(): string
    {
        return Env::get('root_path');
    }

    /**
     * 获取app目录
     * @return string
     */
    public function getAppDir(): string
    {
        return Env::get('app_path');
    }

    /**
     * 获取tp框架目录
     * @return string
     */
    public function getThinkDir(): string
    {
        return Env::get('think_path');
    }

    /**
     * 获取配置目录
     * @return string
     */
    public function getConfigDir(): string
    {
        return Env::get('config_path');
    }

    /**
     * 获取vendor目录
     * @return string
     */
    public function getVendorDir(): string
    {
        return Env::get('vendor_path');
    }

    /**
     * 获取runtime目录
     * @return string
     */
    public function getRuntimeDir(): string
    {
        return Env::get('runtime_path');
    }

    /**
     * 获取路由目录
     * @return string
     */
    public function getRouteDir(): string
    {
        return Env::get('route_path');
    }

    /**
     * 获取当前模块目录
     * @return string
     */
    public function getModuleDir(): string
    {
        return Env::get('module_path');
    }

    public function getEnvInfo(): array
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
        ];
    }
}
