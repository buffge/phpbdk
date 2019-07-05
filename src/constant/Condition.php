<?php
/**
 * Created by IntelliJ IDEA.
 * User: buff
 * Date: 2019/1/26
 * Time: 10:19
 */

namespace bdk\constant;

/**
 * 筛选条件
 * Class Condition
 * @package bdk\constant
 */
abstract class Condition
{
    public const TYPE         = [
        'undefined'    => -1,
        //数值
        'eq'           => 0x0,
        'lt'           => 0x1,
        'gt'           => 0x2,
        'neq'          => 0x3,
        'betweenValue' => 0x4,
        //字符串
        'eqString'     => 0x5,
        'like'         => 0x6,
        'notLike'      => 0x7,
        //时间
        'before'       => 0x8,
        'after'        => 0x9,
        'betweenTime'  => 0xa,
        //数组
        'in'           => 0xb,
        'notIn'        => 0xc,
        'neqString'    => 0xd,

    ];
    public const DB_CONDITION = [
        //数值
        self::TYPE['eq']           => '=',
        self::TYPE['lt']           => '<',
        self::TYPE['gt']           => '>',
        self::TYPE['neq']          => '<>',
        self::TYPE['betweenValue'] => 'BETWEEN',
        //字符串
        self::TYPE['eqString']     => '=',
        self::TYPE['like']         => 'LIKE',
        self::TYPE['notLike']      => 'NOT LIKE',
        //时间
        self::TYPE['before']       => '< TIME',
        self::TYPE['after']        => '> TIME',
        self::TYPE['betweenTime']  => 'BETWEEN TIME',
        //数组
        self::TYPE['in']           => 'IN',
        self::TYPE['notIn']        => 'NOT IN',
        self::TYPE['neqString']    => '<>',

    ];

    public const TYPE_ZH = [
        self::TYPE['undefined']    => '未定义',
        //数值比较
        self::TYPE['eq']           => '等于',
        self::TYPE['lt']           => '小于',
        self::TYPE['gt']           => '大于',
        self::TYPE['neq']          => '不等于',
        self::TYPE['betweenValue'] => '在什么值之间',
        //字符串比较
        self::TYPE['eqString']     => '等于',
        self::TYPE['like']         => '包含',
        self::TYPE['notLike']      => '不包含',
        self::TYPE['neqString']    => '不等于',

        //日期
        self::TYPE['before']       => '在什么日期之前',
        self::TYPE['after']        => '在什么日期之后',
        self::TYPE['betweenTime']  => '在什么日期之间',
        //数组
        self::TYPE['in']           => '在数组中',
        self::TYPE['notIn']        => '不在数组中',
    ];

    /**
     * 格式化
     * @param int $condition
     * @param $val
     * @return string
     */
    public static function formatVal(int $condition, $val)
    {
        switch ($condition) {
            case self::TYPE['like']:
            case self::TYPE['notLike']:
                return '%' . $val . '%';
            default:
                return $val;
        }
    }
}