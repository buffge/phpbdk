<?php

/*
 * Author: buff <admin@buffge.com>
 * Created on : 2018-11-15, 22:43:58
 * QQ:1515888956
 */

namespace bdk\app\common\model;

use bdk\constant\Condition;
use bdk\exception\NotFoundException;
use bdk\traits\ExportConstant;
use bdk\traits\Register;
use Exception;
use think\Model;

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

    protected $globalScope = ['noDel'];

    public function scopeNoDel($query)
    {
        $query->where('dtime', null);
    }

    /**
     * 添加一条数据
     * @param array $data
     * @param bool $needInsertId 是否需要插入的id
     * @param array $allowField 允许插入的字段
     * @return bool|array($insertSuccess,$insertId)
     */
    public static function addItem(array $data, bool $needInsertId = false, array $allowField = [])
    {
        $res = static::create($data, $allowField);
        if ( $res->isEmpty() ) {
            return $needInsertId ? [false, null] : false;
        }
        return $needInsertId ? [true, (int)$res['id']] : true;
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
        if ( is_int($map) ) {
            $formatMap[] = ['id', '=', $map];
        } elseif ( is_array($map) ) {
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
     * @throws Exception
     */
    public static function deleteItem($map, $needDeleteAffectRows = self::NOT_NEED_DELETE_AFFECT_ROWS)
    {
        if ( is_int($map) || is_array($map) && isset($map[0]) && is_int($map[0]) ) {
            $isDeleteSuccess = static::destroy($map);
            $affectRows      = $isDeleteSuccess ? is_int($map) ? 1 : count($map) : 0;
            return $needDeleteAffectRows ? $affectRows : $isDeleteSuccess;
        } elseif ( is_array($map) ) {
            $affectRows = static::where($map)->delete();
            return $needDeleteAffectRows ? $affectRows : $affectRows > 0;
        } else {
            throw new Exception("更新条件只能为id或者where数组");
        }
    }

    /**
     * @param array $map
     * @param array $field
     * @param array $order
     * @return array|\PDOStatement|string|Model|null
     * @throws NotFoundException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getDetail(array $map, array $field = [], array $order = [])
    {
        $res = static::where($map)->field($field)->order($order)->find();
        if ( is_null($res) ) {
            throw new NotFoundException('未查询到数据');
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
        if ( is_null($res) ) {
            throw new NotFoundException('未查询到数据');
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
        if ( $page !== self::NOT_LIMIT ) {
            $query = $query->page($page);
        }
        if ( $limit !== self::NOT_LIMIT ) {
            $query = $query->limit($limit);
        }
        $res = $query->select();
        if ( $res->isEmpty() ) {
            throw new NotFoundException('未查询到数据');
        }
        return $needCount ? [$res, static::where($map)->field($field)->order($order)->count()] : $res;
    }

    public static function getListNotThrowEmptyEx(
        int $page = self::NOT_LIMIT,
        int $limit = self::NOT_LIMIT,
        bool $needCount = self::NOT_NEED_COUNT,
        array $map = [],
        array $field = [],
        array $order = []
    )
    {
        try {
            $res = self::getList($page, $limit, $needCount, $map, $field, $order);
        } catch (NotFoundException $ex) {
            return $needCount ? [[], 0] : [];
        }
        return $res;
    }

    public static function getCount(array $map = []): int
    {
        return static::where($map)->count();
    }

    /**
     * 获取所有未删除的对象
     * @param array $field
     * @return array|Base[]|false
     */
    public static function allNoDel(array $field = [])
    {
        try {
            return static::getList(self::NOT_LIMIT, self::NOT_LIMIT, self::NOT_NEED_COUNT, [
                ['dtime', 'null', ''],
            ], $field);
        } catch (NotFoundException $ex) {
            return [];
        }
    }

    /**
     * 将删除时间改为现在
     * @return bool
     */
    public function nowDelete(): bool
    {
        $this->dtime = date('Y-m-d H:i:s');
        return $this->save();
    }


    public function isDel(): bool
    {
        return $this->getData('dtime') !== null;
    }

    /**
     * 根据filter构建查询条件
     * @param array|null $filterList
     * @param array $filterfieldMapDbField
     * @return array
     */
    public static function buildWhereMap($filterList, array $filterFieldMapDbField): array
    {
        $map = [];
        if ( !is_array($filterList) ) {
            return [];
        }
        foreach ($filterList as $filterField => $item) {
            if ( !is_array($item) || !array_key_exists('condition', $item)
                || !array_key_exists('val', $item) ) {
                continue;
            }
            $item['condition'] = (int)$item['condition'];
            if ( !in_array($item['condition'], Condition::TYPE, true)
                || $item['condition'] === Condition::TYPE['undefined'] ||
                $item['val'] === '' ) {
                continue;
            }
            if ( !array_key_exists($filterField, $filterFieldMapDbField) ) {
                continue;
            }
            $dbField   = $filterFieldMapDbField[$filterField];
            $condition = Condition::DB_CONDITION[$item['condition']];
            $map[]     = [
                $dbField, $condition, Condition::formatVal($item['condition'], $item['val']),
            ];
        }
        return $map;
    }
}
