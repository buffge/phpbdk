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
use bdk\traits\Register;

class Base extends Model implements ArrayAccess, JsonSerializable
{
    use ExportConstant;
    use Register;
    const IS_NOT_DEL = 0x0;
    const IS_DEL = 0x1;
    const NOT_HAVE_PARENT = 0x0;
    protected $jsonKeyArr = [];
    protected $jsonSerializeData = [];
    public function offsetExists($offset): bool
    {
        return $this->getCount([$this->pk => $offset]) === 1;
    }

    public function offsetGet($offset): array
    {
        return $this->getDetail([$this->pk => $offset]);
    }

    public function offsetSet($offset, $value): void
    {
        if (!$this->updateItem([$this->pk => $offset], $value)) {
            throw new UpdateFaildException();
        }
    }

    public function offsetUnset($offset): void
    {
        throw new BadMethodCallException();
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
    public function getDetail(array $map, array $field = [], array $order = []): array
    {
        $whereOr = [];
        if (key_exists('or', $map)) {
            $whereOr = $map['or'];
            unset($map['or']);
        }
        $res = $this->where($map)->whereOr($whereOr)->field($field)->order($order)->find();
        if (is_null($res)) {
            throw new NotFoundException('未查询到此模型数据');
        }
        $resArr = $res->toArray();
        $this->jsonStr2array($resArr, $field, $this->jsonKeyArr);
        return $resArr;
    }

    public function getAll(array $field = []): array
    {
        return $this->getList(
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
    public function getList(
        int $page = CommonConstant::NOT_LIMIT,
            int $limit = CommonConstant::NOT_LIMIT,
            bool $needCount = CommonConstant::NEED_COUNT,
            array $map = [],
        array $field = [],
        array $order = []
    ): array {
        $whereOr = [];
        if (key_exists('or', $map)) {
            $whereOr = $map['or'];
            unset($map['or']);
        }
        if ($page === CommonConstant::NOT_LIMIT || $limit === CommonConstant::NOT_LIMIT) {
            $res = $this->where($map)->whereOr($whereOr)->field($field)->order($order)->select();
        } else {
            $res = $this->where($map)->whereOr($whereOr)->page($page)->
                            limit($limit)->field($field)->order($order)->select();
        }
        if (is_null($res)) {
            throw new NotFoundException('未查询到此模型数据');
        }
        $count = $needCount ? $this->where($map)->whereOr($whereOr)->count() : CommonConstant::UNDEFINED;
        $resArr = [];
        foreach ($res as $v) {
            $item = $v->toArray();
            $this->jsonStr2array($item, $field, $this->jsonKeyArr);
            $resArr[] = $item;
        }
        return $needCount ? [$resArr, $count] : $resArr;
    }

    /**
     * 添加一条数据
     * @param array $data
     * @return bool
     */
    public function addItem(array $data): bool
    {
        return $this->data($data)->isUpdate(false)->save() === true;
    }

    /**
     * 更新一条数据
     * @param array $map
     * @param array $data
     * @return bool
     */
    public function updateItem(array $map, array $data): bool
    {
        $whereOr = [];
        if (key_exists('or', $map)) {
            $whereOr = $map['or'];
            unset($map['or']);
        }
        return $this->where($map)->whereOr($whereOr)->update($data) === 1;
    }

    /**
     * 删除一条数据
     * @param int $id
     * @return bool
     */
    public function deleteItem(int $id): bool
    {
        return $this->where('id', $id)->delete() === 1;
    }

    /**
     *
     * @param array $map
     * @param string $field
     * @return type
     * @throws Exception
     */
    public function getValue(array $map, string $field)
    {
        $whereOr = [];
        if (key_exists('or', $map)) {
            $whereOr = $map['or'];
            unset($map['or']);
        }
        $res = $this->where($map)->whereOr($whereOr)->value($field);
        if (is_null($res)) {
            throw new NotFoundException("未查询到{$field}字段");
        }
        return in_array($field, $this->jsonKeyArr) ? json_decode($res) : $res;
    }

    /**
     * 获取指定条件的总数
     * @param array $map
     * @return int
     */
    public function getCount(array $map = []): int
    {
        $whereOr = [];
        if (key_exists('or', $map)) {
            $whereOr = $map['or'];
            unset($map['or']);
        }
        return $this->where($map)->whereOr($whereOr)->count();
    }

    protected function jsonStr2array(array &$resArr, array $field, array $jsonKeyArr = [])
    {
        foreach ($jsonKeyArr as $jsonKey) {
            if ($field === [] || in_array($jsonKey, $field)) {
                $resArr[$jsonKey] = json_decode($resArr[$jsonKey]);
            }
        }
    }
}
