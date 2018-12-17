<?php

/*
 * Author: buff <admin@buffge.com>
 * Created on : 2018-11-15, 22:43:58
 * QQ:1515888956
 */
namespace bdk\model;

use Exception;
use think\Model;
use bdk\exception\NotFoundException;
use bdk\traits\ExportConstant;
use bdk\traits\Register;

class Base extends Model
{
    use Register;
    use ExportConstant;
    const IS_NOT_DEL                  = 0x0;
    const IS_DEL                      = 0x1;
    const NOT_HAVE_PARENT             = 0x0;
    const NEED_INSERT_ID              = true;
    const NOT_NEED_INSERT_ID          = false;
    const NEED_UPDATE_AFFECT_ROWS     = true;
    const NOT_NEED_UPDATE_AFFECT_ROWS = false;
    const NEED_DELETE_AFFECT_ROWS     = true;
    const NOT_NEED_DELETE_AFFECT_ROWS = false;
    const NOT_LIMIT                   = -1;
    const NEED_COUNT                  = true;
    const NOT_NEED_COUNT              = false;


    /**
     * 添加一条数据
     * @param array $data
     * @param bool $needInsertId 是否需要插入的id
     * @param array $allowField  允许插入的字段
     * @return bool|array($insertSuccess,$insertId)
     */
    public static function addItem(array $data, bool $needInsertId = self::NOT_NEED_INSERT_ID, array $allowField = [])
    {
        $res = static::create($data, $allowField);
        if (empty($res)) {
            return $needInsertId ? [false, null] : false;
        }
        return $needInsertId ? [true, (int) $res['id']] : true;
    }
    /**
     * 根据指定条件更新数据库
     * @param int|array $map
     * @param array $data
     * @return bool|int
     * @throws Exception
     */
    public static function updateItem(
        $map,
        array $data,
        bool $needUpdateAffectRows = self::NOT_NEED_UPDATE_AFFECT_ROWS
    )
    {
        $formatMap = [];
        if (is_int($map)) {
            $formatMap[] = ['id', '=', $map];
        } elseif (is_array($map)) {
            $formatMap = $map;
        } else {
            throw new Exception("更新条件只能为id或者where数组");
        }
        $affectRows = static::where($formatMap)->update($data);
        return $needUpdateAffectRows ? $affectRows : $affectRows > 0;
    }
    /**
     * 根据指定条件删除数据
     * @param int|array $map
     * @param bool $needDeleteAffectRows
     * @return bool|int
     */
    public static function deleteItem($map, $needDeleteAffectRows = self::NOT_NEED_DELETE_AFFECT_ROWS)
    {
        if (is_int($map) || is_array($map) && is_int($map[0])) {
            $isDeleteSuccess = static::destroy($map);
            $affectRows      = $isDeleteSuccess ? is_int($map) ? 1 : count($map) : 0;
            return $needDeleteAffectRows ? $affectRows : $isDeleteSuccess;
        } elseif (is_array($map)) {
            $affectRows = static::where($map)->delete();
            return $needDeleteAffectRows ? $affectRows : $affectRows > 0;
        } else {
            throw new Exception("更新条件只能为id或者where数组");
        }
    }
    public static function getDetail(array $map, array $field = [], array $order = [])
    {
        $res = static::where($map)->field($field)->order($order)->find();
        if (is_null($res)) {
            throw new NotFoundException;
        }
        return $res;
    }
    /**
     *
     * @param array $map
     * @param string $field
     * @return type
     * @throws NotFoundException
     */
    public static function getValue(array $map, string $field)
    {
        $res = static::where($map)->field([$field])->find();
        if (is_null($res)) {
            throw new NotFoundException;
        }
        return $res->getAttr($field);
    }
    public static function getList(
        int $page = self::NOT_LIMIT,
        int $limit = self::NOT_LIMIT,
        bool $needCount = self::NOT_NEED_COUNT,
        array $map = [],
        array $field = [],
        array $order = []
    )
    {
        $query = static::where($map)->field($field)->order($order);
        if ($page !== self::NOT_LIMIT) {
            $query = $query->page($page);
        }
        if ($limit !== self::NOT_LIMIT) {
            $query = $query->limit($limit);
        }
        $res = $query->all();
        if ($res->isEmpty()) {
            throw new NotFoundException;
        }
        return $needCount ? [$res, static::where($map)->field($field)->order($order)->count()] : $res;
    }
    public static function getCount(array $map = []): int
    {
        return static::where($map)->count();
    }
}