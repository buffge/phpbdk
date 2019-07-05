<?php
/**
 * Created by IntelliJ IDEA.
 * User: buff
 * Date: 2018/12/29
 * Time: 17:52.
 */

namespace bdk\app\common\model;
/**
 * 文件
 * Class File
 * @package bdk\app\common\model
 */
class File extends Base
{
    protected $field = [
        'id', 'ctime', 'utime', 'dtime',
        'type', 'path', 'url', 'size',
    ];
    protected $json  = [];
    public const TYPE           = [
        'picture'    => 0x0,
        'doc'        => 0x1,
        'txt'        => 0x2,
        'xlsx'       => 0x3,
        'ppt'        => 0x4,
        'compressed' => 0x5,
        'audio'      => 0x6,
        'video'      => 0x7,
        'bin'        => 0x9,
        'code'       => 0xa,
        'sql'        => 0xb,
        'others'     => 0xc,
    ];
    public const TYPE_ZH        = [
        self::TYPE['picture']    => '图片',
        self::TYPE['docx']       => 'word文档',
        self::TYPE['txt']        => '文本文件',
        self::TYPE['xlsx']       => 'execl文件',
        self::TYPE['pptx']       => 'ppt文件',
        self::TYPE['compressed'] => '压缩文件',
        self::TYPE['audio']      => '音频文件',
        self::TYPE['video']      => '视频文件',
        self::TYPE['bin']        => '二进制文件',
        self::TYPE['code']       => '代码文件',
        self::TYPE['sql']        => '数据库文件',
        self::TYPE['others']     => '其他文件',
    ];
    public const SUFFIX_MAP_URL = [
        //picture
        'jpg'  => '/images/file_icon/picture/jpg.jpg',
        'jpeg' => '/images/file_icon/picture/jpg.jpg',
        'png'  => '/images/file_icon/picture/jpeg.jpg',
        'bmp'  => '/images/file_icon/picture/jpeg.jpg',
        'gif'  => '/images/file_icon/picture/jpeg.jpg',
        //office
        'doc'  => '/images/file_icon/office/doc.jpg',
        'docx' => '/images/file_icon/office/docx.jpg',
        'xls'  => '/images/file_icon/office/xls.jpg',
        'xlsx' => '/images/file_icon/office/xlsx.jpg',
        'ppt'  => '/images/file_icon/office/ppt.jpg',
        'pptx' => '/images/file_icon/office/pptx.jpg',
        //普通文本
        'txt'  => '/images/file_icon/txt.jpg',
        'md'   => '/images/file_icon/md.jpg',
        //compressed
        'zip'  => '/images/file_icon/compressed/zip.jpg',
        'gz'   => '/images/file_icon/compressed/gz.jpg',
        'bz'   => '/images/file_icon/compressed/bz.jpg',
        'bz2'  => '/images/file_icon/compressed/bz2.jpg',
        '7z'   => '/images/file_icon/compressed/7z.jpg',
        'rar'  => '/images/file_icon/compressed/rar.jpg',

        //audio
        'mp3'  => '/images/file_icon/audio/mp3.jpg',
        'ogg'  => '/images/file_icon/audio/ogg.jpg',
        'wav'  => '/images/file_icon/audio/wav.jpg',

        //video
        'mp4'  => '/images/file_icon/video/mp4.jpg',
        'avi'  => '/images/file_icon/video/avi.jpg',
        'rmvb' => '/images/file_icon/video/rmvb.jpg',
        '3gp'  => '/images/file_icon/video/3gp.jpg',
        'flv'  => '/images/file_icon/video/flv.jpg',
        'wmv'  => '/images/file_icon/video/wmv.jpg',
        'swf'  => '/images/file_icon/video/swf.jpg',
        'mov'  => '/images/file_icon/video/mov.jpg',

        //bin
        'exe'  => '/images/file_icon/bin/exe.jpg',
        'apk'  => '/images/file_icon/bin/apk.jpg',
        'ipk'  => '/images/file_icon/bin/ipk.jpg',
        'dll'  => '/images/file_icon/bin/dll.jpg',
        'jar'  => '/images/file_icon/bin/jar.jpg',
        'so'   => '/images/file_icon/bin/so.jpg',
        'a'    => '/images/file_icon/bin/a.jpg',
        'out'  => '/images/file_icon/bin/out.jpg',
        //code
        'php'  => '/images/file_icon/code/php.jpg',
        'js'   => '/images/file_icon/code/js.jpg',
        'json' => '/images/file_icon/code/json.jpg',
        'java' => '/images/file_icon/code/java.jpg',
        'cs'   => '/images/file_icon/code/cs.jpg',
        'c'    => '/images/file_icon/code/c.jpg',
        'cpp'  => '/images/file_icon/code/cpp.jpg',
        'ts'   => '/images/file_icon/code/ts.jpg',
        'sass' => '/images/file_icon/code/sass.jpg',
        'css'  => '/images/file_icon/code/css.jpg',
        'less' => '/images/file_icon/code/less.jpg',
        'jsx'  => '/images/file_icon/code/jsx.jpg',
        'tsx'  => '/images/file_icon/code/tsx.jpg',
        'py'   => '/images/file_icon/code/py.jpg',
        'go'   => '/images/file_icon/code/go.jpg',
        'html' => '/images/file_icon/code/html.jpg',
        'bat'  => '/images/file_icon/code/bat.jpg',
        'ps1'  => '/images/file_icon/code/powershell.jpg',
    ];
}
