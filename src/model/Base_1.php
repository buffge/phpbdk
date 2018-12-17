<?php

/*
 * Author: buff <admin@buffge.com>
 * Created on : 2018-11-15, 22:43:58
 * QQ:1515888956
 */
namespace bdk\model;

use ArrayAccess;
use JsonSerializable;
use Exception;
use BadMethodCallException;
use think\Model;
use bdk\exception\NotFoundException;
use bdk\exception\UpdateFaildException;
use bdk\constant\Common as CommonConstant;
use bdk\traits\ExportConstant;

class Base1 extends Model implements ArrayAccess, JsonSerializable
{

    use ExportConstant;
    const IS_NOT_DEL      = 0x0;
    const IS_DEL          = 0x1;
    const NOT_HAVE_PARENT = 0x0;

    protected $jsonSerializeData = [];

    public function offsetExists($id): bool
    {
        return static::getCount([$this->pk => $id]) === 1;
    }

    public function offsetGet($id)
    {
        return $this->getDetail([$this->pk => $id]);
    }

    public function offsetSet($id, $value): void
    {
        if (!$this->updateItem([$this->pk => $id], $value)) {
            throw new UpdateFaildException();
        }
    }

    public function offsetUnset($id): void
    {
        throw new BadMethodCallException($id);
    }

    public function jsonSerialize()
    {
        return $this->jsonSerializeData;
    }

    public function setJsonData(array $data): void
    {
        $this->jsonSerializeData = $data;
    }

    public function clearJsonData(): void
    {
        $this->jsonSerializeData = [];
    }

    /**
     * 获取详情
     * @param array $map
     * @param array $field
     * @param array $order
     * @return array
     * @throws Exception
     */
    public static function getDetail(array $map, array $field = [], array $order = []): array
    {
        $mdb     = self::getModelDb();
        $whereOr = [];
        if (key_exists('or', $map)) {
            $whereOr = $map['or'];
            unset($map['or']);
        }
        $res = $mdb->where($map)->whereOr($whereOr)->field($field)->order($order)->find();
        if (is_null($res)) {
            throw new NotFoundException('未查询到此模型数据');
        }
        $resArr = $res->toArray();
        self::jsonStr2array($resArr, $field, static::$jsonKeyArr);
        return $resArr;
    }

    public static function getAll(array $field = []): array
    {
        return static::getList(
                        CommonConstant::NOT_LIMIT,
                        CommonConstant::NOT_LIMIT,
                        CommonConstant::NOT_NEED_COUNT,
                        [],
                        $field
        );
    }

    /**
     * 获取列表
     * @param int $page
     * @param int $limit
     * @param bool $needCount
     * @param array $map
     * @param array $field
     * @param array $order
     * @return array
     * @throws Exception
     */
    public static function getList(
            int $page = CommonConstant::NOT_LIMIT,
            int $limit = CommonConstant::NOT_LIMIT,
            bool $needCount = CommonConstant::NEED_COUNT,
            array $map = [], array $field = [], array $order = []
    ): array
    {
        $mdb     = self::getModelDb();
        $whereOr = [];
        if (key_exists('or', $map)) {
            $whereOr = $map['or'];
            unset($map['or']);
        }
        if ($page === CommonConstant::NOT_LIMIT || $limit === CommonConstant::NOT_LIMIT) {
            $res = $mdb->where($map)->whereOr($whereOr)->field($field)->order($order)->select();
        } else {
            $res = $mdb->where($map)->whereOr($whereOr)->page($page)->
                            limit($limit)->field($field)->order($order)->select();
        }
        if (is_null($res)) {
            throw new NotFoundException('未查询到此模型数据');
        }
        $count  = $needCount ? $mdb->where($map)->whereOr($whereOr)->count() : CommonConstant::UNDEFINED;
        $resArr = [];
        foreach ($res as $v) {
            $item     = $v->toArray();
            self::jsonStr2array($resArr, $field, static::$jsonKeyArr);
            $resArr[] = $item;
        }
        return $needCount ? [$resArr, $count] : $resArr;
    }

    /**
     * 添加一条数据
     * @param array $data
     */
    public static function addItem(array $data, bool $needInsertId = false, array $allowField = [])
    {
        $res = self::create($data, $allowField);
        
    }

    /**
     * 更新一条数据
     * @param array $map
     * @param array $data
     * @return bool
     */
    public static function updateItem(array $map, array $data): bool
    {
        $mdb     = self::getModelDb();
        $whereOr = [];
        if (key_exists('or', $map)) {
            $whereOr = $map['or'];
            unset($map['or']);
        }
        return $mdb->where($map)->whereOr($whereOr)->update($data) === 1;
    }

    /**
     * 删除一条数据
     * @param int $id
     * @return bool
     */
    public static function deleteItem(int $id): bool
    {
        $mdb = self::getModelDb();
        return $mdb->where($mdb->getPk(), $id)->delete() === 1;
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
        $mdb     = self::getModelDb();
        $whereOr = [];
        if (key_exists('or', $map)) {
            $whereOr = $map['or'];
            unset($map['or']);
        }
        $res = $mdb->where($map)->whereOr($whereOr)->value($field);
        if (is_null($res)) {
            throw new NotFoundException("未查询到{$field}字段");
        }
        return in_array($field, static::$jsonKeyArr) ? json_decode($res) : $res;
    }

    /**
     * 获取指定条件的总数
     * @param array $map
     * @return int
     */
    public static function getCount(array $map = []): int
    {
        $mdb     = self::getModelDb();
        $whereOr = [];
        if (key_exists('or', $map)) {
            $whereOr = $map['or'];
            unset($map['or']);
        }
        return $mdb->where($map)->whereOr($whereOr)->count();
    }

    /**
     * 解码所有数据库的json字段
     * @param array $resArr
     * @param array $field
     * @param array $jsonKeyArr
     */
    protected static function jsonStr2array(array &$resArr, array $field, array $jsonKeyArr = [])
    {
        foreach ($jsonKeyArr as $jsonKey) {
            if ($field === [] || in_array($jsonKey, $field)) {
                $resArr[$jsonKey] = json_decode($resArr[$jsonKey]);
            }
        }
    }

}
