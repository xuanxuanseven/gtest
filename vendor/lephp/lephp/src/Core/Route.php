<?php
/**
 * Created by PhpStorm.
 * User: wanglishuang
 * Date: 2016/6/8
 * Time: 15:30
 */
namespace Lephp\Core;
use Yaf\Loader;

class Route {
    const BEFORE_PARSE_URI  = 'before_parse_uri',
        BEFORE_ROUTE        = 'before_route',
        BEFAULT_EXECUTE     = 'before_execute',
        AFTER_EXECUTE       = 'after_execute';

    const CONTROLLER_NOT_FOUND_ERRNO    = 404;

    public static $base_url = '/';
    public static $uri;
    public static $plugin	= array();
    public static $plugin_order;

    public static $controller;
    public static $controller_class;
    public static $controller_prefix	= 'Controller';
    public static $default_controller	= 'Home';

    const REQUEST_PATTERN = '|^(?P<controller>(/{1}[a-z0-9]+){0,5})/?$|uD';

    /**
     * 封装请求的全部步骤，解析URI、分发、处理请求
     *
     * @return void
     */
    public static function dispatch() {
        self::parse_uri();
        self::route();
        self::execute();
    }

    /**
     * 解析 URI，如果为命令行请求同时初始化GET和POST参数
     *
     * @return void
     */
    public static function parse_uri() {
        self::run_plugin(self::BEFORE_PARSE_URI);
        $uri = self::detect_uri();

        $uri = preg_replace('#//+#', '/', $uri);
        $uri = preg_replace('#\.[\s./]*/#', '', $uri);
        $uri = trim($uri, '/');
        if(self::$base_url && 0 === substr_compare($uri, self::$base_url, 0, strlen(self::$base_url))){
            $uri = trim(substr($uri, strlen(self::$base_url)), '/');
        }

        self::$uri = $uri;
    }

    /**
     * 在请求正式执行前需要执行的程序
     *
     * 主要是执行event
     * 解析 controller 后续execute阶段用到
     */
    public static function route() {
        self::run_plugin(self::BEFORE_ROUTE);

        if (!self::match(self::$uri)) {
            throw new Exception('URI error '.self::$uri);
        }

        $tmp = explode('/', self::$controller);
        $tmp = array_map('ucfirst', $tmp);
        self::$controller_class = implode('', $tmp);
        unset($tmp);

        if (!empty(self::$controller_prefix)) {
            self::$controller_class = self::$controller_prefix . '' . self::$controller_class;
        }
    }

    /**
     * 处理请求, 执行controller里面的run函数，其中controller由router得出
     * 同时执行event
     * 默认地,所有的从controller输出的部分都被捕获并返回，
     *
     * 无http头发送
     *
     * @throws  Exception
     */
    public static function execute() {
        self::run_plugin(self::BEFAULT_EXECUTE);
        if (!class_exists(self::$controller_class)) {
            throw new Exception('Not Found '.self::$uri, self::CONTROLLER_NOT_FOUND_ERRNO);
        }
        /* @var SController $class */
        new self::$controller_class;
        self::run_plugin(self::AFTER_EXECUTE);
    }

    /**
     * 自动的使用 PATH_INFO,REQUEST_URI, PHP_SELF or REDIRECT_URL获取请求URI,
     *
     * @return  string  URI
     * @throws  Exception
     */
    public static function detect_uri() {
        // 不受base_url影响
        if (!empty($_SERVER['PATH_INFO'])) {
            $uri = $_SERVER['PATH_INFO'];
        } else {
            if (isset($_SERVER['REQUEST_URI'])) {
                // 提取path部分
                $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
                $uri = rawurldecode($uri);
            } elseif (isset($_SERVER['PHP_SELF'])) {
                $uri = $_SERVER['PHP_SELF'];
            } elseif (isset($_SERVER['REDIRECT_URL'])) {
                $uri = $_SERVER['REDIRECT_URL'];
            } else {
                throw new Exception('can not detect uri');
            }

            $base_url = rtrim(self::$base_url, '/') . '/';
            $base_url = parse_url($base_url, PHP_URL_PATH);

            if (strpos($uri, $base_url) === 0) {
                // Remove the base URL from the URI
                $uri = substr($uri, strlen($base_url));
            }
        }

        return $uri;
    }

    /**
     * 将URL转换为controller
     *
     * @param string  $uri
     * @return bool
     */
    private static function match($uri) {
        // URI不区分大小写，明确的界定符/，确保正则匹配
        $uri = '/' . trim(strtolower($uri), '/');
        if (preg_match(self::REQUEST_PATTERN, $uri, $matches)) {
            self::$controller	= empty($matches['controller']) ? self::$default_controller : trim($matches['controller'], '/');
            $cil_path =  APPLICATION_PATH . "/" . APP_NAME . "/modules/" . ucfirst(PHP_SAPI) . $matches['controller'].'.php';
            if (file_exists($cil_path)) {
                Loader::import($cil_path);
            } else {
                return false;
            }
            return true;
        } else {
            return false;
        }
    }

    /**
     * 添加event
     *
     * @param string $when before_parse_uri, after_parse_uri, before_route, after_route, before_execute, before_execute
     * @param SPlugin $event实例
     * @throws Exception
     */
    public static function add_plugin($when, Dplugin $event) {
        if (!in_array($when, array(self::BEFORE_PARSE_URI, self::BEFORE_ROUTE, self::BEFAULT_EXECUTE, self::AFTER_EXECUTE))) {
            throw new Exception('invalid event position');
        }

        $event_name = $event->get_name();
        if (isset(self::$plugin[$event_name])) {
            throw new Exception('the event added already');
        }

        self::$plugin_order[$when][]	= $event_name;
        self::$plugin[$event_name]	= $event;
    }

    /**
     * 获取event
     *
     * @param string $name
     * @throws Exception
     * @return Dplugin
     */
    public static function get_plugin($name) {
        if (!isset(self::$plugin[$name])) {
            throw new Exception("Event {$name} not exists");
        }

        return self::$plugin[$name];
    }

    /**
     * 调用各阶段插件
     *
     * @param string $when
     */
    public static function run_plugin($when) {
        if (isset(self::$plugin_order[$when])) {
            foreach (self::$plugin_order[$when] as $event) {
                self::get_plugin($event)->run();
            }
        }
    }

    /**
     * @static
     * @param string $controller
     * @param string $query
     * @return string
     */
    public static function get_url($controller, $query='') {

    }
}