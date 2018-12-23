<?php

/*
 * Author: buff <admin@buffge.com>
 * Created on : 2018-6-11, 10:39:45
 * QQ:1515888956
 */
namespace bdk;

use bdk\exception\RegisterModeException;

/**
 * 注册树
 */
class Register
{
    protected static $objects = [];
    protected static $nameAlias = [];
    /**
     * 向树中注册一个实例
     * @param string $instanceAlias
     * @param type $object
     * @throws RegisterModeException
     */
    public static function push(string $instanceAlias, $object)
    {
        if (self::isset($instanceAlias)) {
            throw new RegisterModeException(
                "将实例注册到注册树失败,实例别名{$instanceAlias}在注册树中已存在\n",
                                            RegisterModeException::ALIAS_ALREADY_EXISTED
            );
        }
        self::$objects[$instanceAlias] = $object;
    }

    /**
     * 根据键或别名删除实例
     * @param string $instanceAlias
     * @throws RegisterModeException
     */
    public static function delete(string $instanceAlias)
    {
        if (self::isset($instanceAlias)) {
            unset(self::$objects[$instanceAlias]);
            if (in_array($instanceAlias, self::$nameAlias)) {
                unset(self::$nameAlias[array_search($instanceAlias, self::$nameAlias)]);
            }
        } elseif (key_exists($instanceAlias, self::$nameAlias)) {
            unset(self::$objects[self::$nameAlias[$instanceAlias]]);
            unset(self::$nameAlias[$instanceAlias]);
        }
        throw new RegisterModeException(
            "删除实例失败,实例别名{$instanceAlias}在注册树中不存在\n",
                                        RegisterModeException::ALIAS_NOT_FOUND
        );
    }

    /**
     * 根据键名或别名获取实例
     * @param string $instanceAlias
     * @return type
     * @throws RegisterModeException
     */
    public static function get(string $instanceAlias)
    {
        if (self::isset($instanceAlias)) {
            return self::$objects[$instanceAlias];
        } elseif (key_exists($instanceAlias, self::$nameAlias)) {
            return self::get(self::$nameAlias[$instanceAlias]);
        }
        throw new RegisterModeException(
            "获取实例失败,实例别名{$instanceAlias}在注册树中不存在\n",
                                        RegisterModeException::ALIAS_NOT_FOUND
        );
    }

    /**
     * 判断注册树中是否有某个键或别名
     * @param string $key
     * @return bool
     */
    public static function isset(string $key): bool
    {
        return key_exists($key, self::$objects) || key_exists($key, self::$nameAlias);
    }

    /**
     * 设置别名
     * @param string $alias
     * @param string $key
     * @return bool
     */
    public static function setAlias(string $alias, string $key): bool
    {
        if (!key_exists($key, self::$objects)) {
            return false;
        }
        self::$nameAlias[$alias] = $key;
        return true;
    }
}
