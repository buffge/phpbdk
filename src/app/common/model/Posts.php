<?php


namespace bdk\app\common\model;

/**
 * Class Post
 * @package bdk\app\common\model
 */
class Posts extends Base
{
    protected $field = [
        'id', 'ctime', 'utime', 'dtime',
        'author_uid',
        'title', 'content',
        // 摘要
        'excerpt',
        'type',
        'mime_type',// 文件类型
        'sort',// 排序
        'status', 'name', 'view_count',
        'category_id',
        'source_name',
        'source_url',
        'pic_id_list',
    ];
    protected $json  = ['pic_id_list'];

    public const STATUS    = [
        'publish'    => 0x0,// 发布
        'draft'      => 0x1,// 草稿
        'auto-draft' => 0x2,// 自动草稿
    ];
    public const STATUS_ZH = [
        self::STATUS['publish']    => '发布',
        self::STATUS['draft']      => '草稿',
        self::STATUS['auto-draft'] => '自动草稿',
    ];
    /**
     * 类型
     */
    public const TYPE    = [
        'article'    => 0x0,
        'text'       => 0x1,
        'int'        => 0x2,
        'double'     => 0x3,
        'json'       => 0x4,
        'pic'        => 0x5,
        'attachment' => 0x6,// 附件
    ];
    public const TYPE_ZH = [
        self::TYPE['article']    => '文章',
        self::TYPE['text']       => '文本',
        self::TYPE['int']        => '整数',
        self::TYPE['double']     => '浮点数',
        self::TYPE['json']       => 'json',
        self::TYPE['pic']        => '图片',
        self::TYPE['attachment'] => '附件',
    ];

    /**
     * 作者
     * @return \think\model\relation\HasOne
     */
    public function author()
    {
        return $this->hasOne(User::class, 'id', 'author_uid');
    }

    /**
     * 分类
     * @return \think\model\relation\HasOne
     */
    public function category()
    {
        return $this->hasOne(PostsCategory::class, 'id', 'category_id');
    }


    /**
     * 文章标签列表
     * @return \think\model\relation\HasMany
     */
    public function tags()
    {
        return $this->hasMany(PostsTag::class, 'posts_id', 'id');
    }

    /**
     * @param array $field
     * @return array|\PDOStatement|string|\think\Collection
     */
    public function pictures($field = ['id', 'url', 'title'])
    {
        $picIdList = $this->getData('pic_id_list');
        if ( !is_array($picIdList) ) {
            return [];
        }
        $list = Picture::getListNotThrowEmptyEx(Picture::NOT_LIMIT, Picture::NOT_LIMIT,
            Picture::NOT_NEED_COUNT, [
                ['id', 'in', $picIdList],
            ], $field);

        return $list;
    }
}