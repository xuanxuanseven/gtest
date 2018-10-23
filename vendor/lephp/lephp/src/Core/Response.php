<?php
/**
 * HTTP响应
 * Created for LeEco User Center
 * User: Yishu Gong<gongyishu@le.com>
 * Date: 2016/4/27
 * Time: 18:20
 * @copyright LeEco
 * @since 1.0.0
 */

namespace Lephp\Core;

use Yaf\Response_Abstract;

class Response extends Response_Abstract
{
    // 输出数据的转换方法
    protected static $transform = null;
    // 输出数据的类型
    protected static $type = '';
    // 输出数据
    protected static $data = '';
    // 是否exit
    protected static $isExit = false;

    static protected $statusTexts = [
        '100' => 'Continue',
        '101' => 'Switching Protocols',
        '200' => 'OK',
        '201' => 'Created',
        '202' => 'Accepted',
        '203' => 'Non-Authoritative Information',
        '204' => 'No Content',
        '205' => 'Reset Content',
        '206' => 'Partial Content',
        '300' => 'Multiple Choices',
        '301' => 'Moved Permanently',
        '302' => 'Found',
        '303' => 'See Other',
        '304' => 'Not Modified',
        '305' => 'Use Proxy',
        '306' => '(Unused)',
        '307' => 'Temporary Redirect',
        '400' => 'Bad Request',
        '401' => 'Unauthorized',
        '402' => 'Payment Required',
        '403' => 'Forbidden',
        '404' => 'Not Found',
        '405' => 'Method Not Allowed',
        '406' => 'Not Acceptable',
        '407' => 'Proxy Authentication Required',
        '408' => 'Request Timeout',
        '409' => 'Conflict',
        '410' => 'Gone',
        '411' => 'Length Required',
        '412' => 'Precondition Failed',
        '413' => 'Request Entity Too Large',
        '414' => 'Request-URI Too Long',
        '415' => 'Unsupported Media Type',
        '416' => 'Requested Range Not Satisfiable',
        '417' => 'Expectation Failed',
        '500' => 'Internal Server Error',
        '501' => 'Not Implemented',
        '502' => 'Bad Gateway',
        '503' => 'Service Unavailable',
        '504' => 'Gateway Timeout',
        '505' => 'HTTP Version Not Supported',
    ];

    /**
     * (Yaf >= 2.2.9)
     * 响应报头
     *
     * @var array
     */
    protected $_header;

    /**
     * (Yaf >= 2.2.9)
     * 响应正文
     *
     * @var array
     */
    protected $_body;

    /**
     * (Yaf >= 2.2.9)
     * 是否开启已输出响应报头检测
     *
     * @var Int
     */
    protected $_sendheader = 0;

    /**
     * (Yaf >= 2.2.9)
     * 响应状态码
     *
     * @var Int
     */
    protected $_response_code;

    /**
     * (Yaf >= 2.2.9)
     * 默认响应正文实体名
     *
     * @var string
     */
    public $DEFAULT_BODY = 'content';

    /**
     * (Yaf >= 2.2.9)
     * 构造方法
     *
     */
    public function __construct()
    {
    }

    /**
     * (Yaf >= 2.2.9)
     * 析构方法
     */
    public function __destruct()
    {
    }

    /**
     * (Yaf >= 2.2.9)
     * 重置__clone魔术方法
     *
     * @return void
     */
    private function __clone()
    {
    }

    /**
     * (Yaf >= 2.2.9)
     * 返回响应正文的字符串
     *
     * @return String
     */
    public function __toString()
    {
    }

    /**
     * (Yaf >= 2.2.9)
     * 设置类型为$name的响应正文内容
     *
     * @param string $body 响应正文内容（可覆盖原来的）
     * @param string $name 响应正文类型，默认为content
     *
     * @return Boolean
     */
    public function setBody($body, $name = 'content')
    {
    }

    /**
     * (Yaf >= 2.2.9)
     * 获取类型为$name的响应正文内容
     *
     * @param string $name 响应正文类型，默认为content
     *
     * @return String
     */
    public function getBody($name = 'content')
    {
    }

    /**
     * (Yaf >= 2.2.9)
     * 设置类型为$name的响应正文内容, 如已存在，则追加到原来正文的后面
     *
     * @param string $body 响应正文内容（可追加）
     * @param string $name 响应正文类型，默认为content
     *
     * @return Boolean
     */
    public function appendBody($body, $name = 'content')
    {
    }

    /**
     * (Yaf >= 2.2.9)
     * 设置类型为$name的响应正文内容, 如已存在，则追加到原来正文的前面
     *
     * @param string $body 响应正文内容（可追加）
     * @param string $name 响应正文类型，默认为content
     *
     * @return Boolean
     */
    public function prependBody($body, $name = 'content')
    {
    }

    /**
     * (Yaf >= 2.2.9)
     * 清空响应正文
     *
     * @deprecated 总是返回false
     *
     * @return Boolean
     */
    public function clearBody($name = null)
    {
    }

    /**
     * (Yaf >= 2.2.9)
     * 获取响应报头
     *
     * @deprecated 总是返回null
     *
     * @return null
     */
    public function getHeader()
    {
    }

    /**
     * (Yaf >= 2.2.9)
     * 获取所有响应报头
     *
     * @deprecated 总是返回false
     *
     * @return Boolean
     */
    public function setAllHeaders()
    {
    }

    /**
     * (Yaf >= 2.2.9)
     * 设置响应报头
     *
     * @deprecated 总是返回null
     *
     * @return null
     */
    public function setHeader()
    {
    }

    /**
     * (Yaf >= 2.2.9)
     * 清空响应报头
     *
     * @deprecated 总是返回false
     *
     * @return Boolean
     */
    public function clearHeaders()
    {
    }

    /**
     * (Yaf >= 2.2.9)
     * 将当前请求重定向到指定的URL（内部实现是通过发送Location报头实现，如：header("Location:http//www.phpboy.net/"））
     *
     * @param string $url 重定向的绝对URL
     *
     * @return Boolean
     */
    public function setRedirect($url)
    {
    }

    /**
     * (Yaf >= 2.2.9)
     * 输出所有的响应正文
     *
     * @return Boolean
     */
    public function response()
    {
    }

    /**
     * Forces the user's browser not to cache the results of the current request.
     *
     * @return void
     * @access protected
     * @link http://book.cakephp.org/view/431/disableCache
     */
    public static function disableBrowserCache()
    {
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header("Cache-Control: no-store, no-cache, must-revalidate");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");
    }

    /**
     * 发送数据到客户端
     * @access protected
     * @param mixed $data 要返回的数据
     * @param String $type 返回数据格式
     * @param bool $return 是否返回数据
     * @return mixed
     */
    public static function send($data = '', $type = '', $return = false)
    {
        $type = strtolower($type ?: self::$type);

        $headers = [
            'json'   => 'application/json',
            'xml'    => 'text/xml',
            'html'   => 'text/html',
            'jsonp'  => 'application/javascript',
            'script' => 'application/javascript',
            'text'   => 'text/plain',
        ];

        if (!headers_sent() && isset($headers[$type])) {
            header('Content-Type:' . $headers[$type] . '; charset=utf-8');
        }

        $data = $data ?: self::$data;
        if (is_callable(self::$transform)) {
            $data = call_user_func_array(self::$transform, [$data]);
        } else {
            switch ($type) {
                case 'json':
                    // 返回JSON数据格式到客户端 包含状态信息
                    $data = json_encode($data, JSON_UNESCAPED_UNICODE);
                    break;
                case 'jsonp':
                    // 返回JSON数据格式到客户端 包含状态信息
                    $handler = !empty($_GET[Tool::getConfig('var_jsonp_handler')]) ? $_GET[Tool::getConfig('var_jsonp_handler')] : Tool::getConfig('default_jsonp_handler');
                    $data = $handler . '(' . json_encode($data, JSON_UNESCAPED_UNICODE) . ');';
                    break;
            }
        }

        if ($return) {
            return $data;
        }
        echo $data;
        self::isExit() && exit();
    }

    /**
     * 转换控制器输出的数据
     * @access public
     * @param mixed $callback 调用的转换方法
     * @return void
     */
    public static function transform($callback)
    {
        self::$transform = $callback;
    }

    /**
     * 输出类型设置
     * @access public
     * @param string $type 输出内容的格式类型
     * @return mixed
     */
    public static function type($type = null)
    {
        if (is_null($type)) {
            return self::$type ?: Tool::getConfig('default_return_type');
        }
        self::$type = $type;
    }

    /**
     * 输出数据设置
     * @access public
     * @param mixed $data 输出数据
     * @return void
     */
    public static function data($data)
    {
        self::$data = $data;
    }

    /**
     * 输出是否exit设置
     * @access public
     * @param bool $exit 是否退出
     * @return mixed
     */
    public static function isExit($exit = null)
    {
        if (is_null($exit)) {
            return self::$isExit;
        }
        self::$isExit = (boolean)$exit;
    }
}