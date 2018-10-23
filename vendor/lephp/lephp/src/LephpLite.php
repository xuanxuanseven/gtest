<?php
/**
 * Created by PhpStorm.
 * User: marlin
 * Date: 2016/11/14
 * Time: 下午4:28
 */

namespace Lephp;

use Lephp\Core\Request;
use Lephp\Core\Tool;
use Lephp\Plugins\LogPlugin;
use Yaf\Application;
use Yaf\Dispatcher;
use Yaf\Registry;
use Yaf\Request\Simple;
use Yaf\Route\Regex;

if (!defined('APP_PATH')) {
    define('APP_PATH', realpath(dirname(__FILE__) . '/../../../'));
}
if (!defined('APP_NAME')) {
    define('APP_NAME', 'application');
}
if (!defined('APPLICATION_PATH')) {
    define('APPLICATION_PATH', APP_PATH);
}
if (!defined('APPLICATION_INI')) {
    define('APPLICATION_INI', APP_PATH . "/conf/application.ini");
}
if (!defined('APP_START_TIME')) {
    list($t1, $t2) = explode(' ', microtime());
    $startTime = sprintf('%d', (floatval($t1) + floatval($t2)) * 1000);
    define('APP_START_TIME', $startTime);
}
if (!defined('APP_DEBUG')) {
    define('APP_DEBUG', false);
}

if (!defined('IS_AJAX')) {
    define('IS_AJAX',
           (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') ? true : false);
}

// 环境常量
if (!defined('IS_CLI')) {
    // 是否是命令行调用
    define('IS_CLI', PHP_SAPI == 'cli' ? 1 : 0);
}
if (!defined('REQUEST_METHOD')) {
    // 请求 method
    define('REQUEST_METHOD', IS_CLI ? 'GET' : $_SERVER['REQUEST_METHOD']);
}
if (!defined('IS_GET')) {
    // 是否是GET
    define('IS_GET', REQUEST_METHOD == 'GET' ? true : false);
}
if (!defined('IS_POST')) {
    define('IS_POST', REQUEST_METHOD == 'POST' ? true : false);
}
if (!defined('IS_PUT')) {
    define('IS_PUT', REQUEST_METHOD == 'PUT' ? true : false);
}
if (!defined('IS_DELETE')) {
    define('IS_DELETE', REQUEST_METHOD == 'DELETE' ? true : false);

}
if (!defined('LOG_PATH')) {
    // 默认日志目录
    /**
     * @since 1.1.0
     */
    define('LOG_PATH', '/letv/wwwlogs/' . APP_NAME);
}

class LephpLite extends BaseLephp
{
    /**
     * @param string $defaultVersion 默认版本
     */
    public static function run($defaultVersion = 'v1')
    {
        global $lephpRequestCosts;
        $lephpRequestCosts = [];
        $app = new Application(APPLICATION_INI);
        // 注册运行版本
        $request = new Request();
        $version = $request->getRequestVersion($defaultVersion);
        Registry::set("requestVersion", strtolower($version));
        /**
         * 加载默认配置文件, 请确保存在这个配置文件
         */
        require APPLICATION_PATH . '/conf/define.php';
        if (1 === IS_CLI) {
            global $argc, $argv;
            $app->bootstrap()->execute('\Lephp\LephpLite::execute', $argc, $argv);
        } else {
            $app->bootstrap()->run();
        }
    }

    /**
     * @param \Yaf\Dispatcher $dispatcher
     * @throws \Exception
     */
    public static function bootstrap($dispatcher)
    {
        global $argc, $argv;
        $cliHelpInfo =
            "\n========================================================================\n" .
            "| Insufficient Param!                                                  |\n" .
            "| Cli Mode need At least 2 Params!                                     |\n" .
            "| Example:                                                             |\n" .
            "| /path/to/php /path/to/cli.php --controller=consumer --action=index   |\n" .
            "========================================================================\n";
        if (IS_CLI && $argc > 1) {
            $shortOpts = "";
            $shortOpts .= "c:";
            $shortOpts .= "a:";
            $shortOpts .= "P::"; // parameters
            $shortOpts .= "m::"; //module
            $longOpts = [
                'controller:',
                'action:',
                'module::',
                'params::'
            ];
            $defaults = [
                'module' => 'Cli'
            ];
            $shortLong = [
                'c' => 'controller',
                'a' => 'action',
                'P' => 'params',
                'm' => 'module'
            ];
            $options = getopt($shortOpts, $longOpts) + $defaults;
            foreach ($shortLong as $short => $long) {
                if (isset($options[$long])) {
                    $options[$short] = $options[$long];
                } elseif (isset($options[$short]) && !isset($options[$long])) {
                    $options[$long] = $options[$short];
                }
            }
            if (isset($options['controller']) && isset($options['action'])) {
                $request = new Simple('GET', 'Cli', isset($options['controller']) ? $options['controller'] : 'index',
                                      isset($options['action']) ? $options['action'] : 'index',
                                      isset($options['params']) ? $options['params'] : []);
                $dispatcher->disableView();
                $dispatcher->setRequest($request);
            } else {
                throw new \Exception($cliHelpInfo);
            }
        } elseif (IS_CLI && $argc <= 1) {
            throw new \Exception($cliHelpInfo);
        }
        // 加载配置文件
        self::loadConfig();
        // 初始化路由配置
        self::initRoute($dispatcher);
        // 初始化 localName
        self::initLocalName();
        // 初始化插件
        self::initPlugins($dispatcher);
        // 初始化模板引擎
        self::initViewTemplateEngine($dispatcher);

        // 注册配置
        $arrConfig = Application::app()->getConfig();
        Registry::set(
            'config',
            $arrConfig
        );
    }

    /**
     * 加载配置文件
     */
    private static function loadConfig()
    {
    }

    /**
     * @param Dispatcher $dispatcher
     * @throws \Exception
     */
    private static function initRoute($dispatcher)
    {
        $dispatcher->catchException(true);
        $config = Registry::get("moduleConfig");
        /**
         * @var \Yaf\Router $router
         */
        $router = $dispatcher->getRouter();
        $moduleName = $config['module'];
        $urlRules = $config['urlManager']['rules'];
        if (!$urlRules) {
            throw new \Exception('Not Found', 404);
        }
        foreach ($urlRules as $key => $rule) {
            $route = new Regex($rule['match'], $rule['route'], $rule['map'], $rule = ['verify']);
            $router->addRoute($moduleName . '_' . $key, $route);
        }
    }

    /**
     * @param \Yaf\Dispatcher $dispatcher
     */
    private static function initPlugins($dispatcher)
    {
        $dispatcher->registerPlugin(new LogPlugin());
        $dispatcher->registerPlugin(new PluginInjectPlugin());
    }

    /**
     *
     */
    private static function initLocalName()
    {
    }

    /**
     * @param Dispatcher $dispatcher
     * @param string $templateEngineName
     */
    private static function initViewTemplateEngine($dispatcher, $templateEngineName = '')
    {
        $dispatcher->disableView();
    }

    public static function execute($argc, $argv)
    {
        if (IS_CLI) {
            /** @var Dispatcher $dispatcher */
            $dispatcher = Application::app()->getDispatcher();
            $dispatcher->dispatch($dispatcher->getRequest());
            echo "Run Complete\n";
            echo "Total Costs: " . (Tool::getMilliSecond() - APP_START_TIME) . "ms \n";
            exit(1);
        } else {
            throw new \Exception('Call this in cli mode!');
        }
    }

    /**
     * Just for Unit Test
     * @param $request
     * @param string $version
     */
    public static function runUnitTest($request, $version = 'v1')
    {
        define('IS_UNIT_TEST', true);
        /** @var \Yaf\Dispatcher $dispatcher */
        global $lephpRequestCosts;
        $lephpRequestCosts = [];
        $app = new Application(APPLICATION_INI);
        // 注册运行版本
        Registry::set("requestVersion", strtolower($version));
        /**
         * 加载默认配置文件, 请确保存在这个配置文件
         */
        require APPLICATION_PATH . '/conf/define.php';
        $dispatcher = $app->getDispatcher();
        $dispatcher->setRequest($request);
        $app->bootstrap();
    }

    /**
     * Just for Unit Test
     * @param $dispatcher
     */
    public static function bootstrapUnitTest($dispatcher)
    {
        // 加载配置文件
        self::loadConfig();
        // 初始化路由配置
        self::initRoute($dispatcher);
        // 初始化 localName
        self::initLocalName();
        // 初始化插件
        self::initPlugins($dispatcher);
        // 初始化模板引擎
        self::initViewTemplateEngine($dispatcher);

        // 注册配置
        $arrConfig = Application::app()->getConfig();
        Registry::set(
            'config',
            $arrConfig
        );
    }
}