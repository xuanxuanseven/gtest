<?php
namespace Lephp\Plugins;

use Yaf\Config\Ini;
use Yaf\Dispatcher;
use Yaf\Plugin_Abstract;
use Yaf\Request_Abstract;
use Yaf\Response_Abstract;

/**
 * Created by PhpStorm.
 * User: marlin
 * Date: 2016/11/10
 * Time: 下午6:16
 * @property \Yaf\Config\Ini $config
 * @property string $method Request Method, POST | GET | PUT | PATCH | DELETE | OPTIONS
 * @property string $contentType
 * @property \Yaf\Request_Abstract $request
 *
 */
class RestPlugin extends Plugin_Abstract
{
    public $config;

    private $method;

    private $contentType;

    public $request;

    /**
     * RestPlugin constructor.
     * @param Ini | null $userDefinedConf
     */
    public function __construct($userDefinedConf = null)
    {
        if (is_null($userDefinedConf)) {
            try {
                $this->config = new Ini(APP_PATH . "/conf/rest.ini", 'common');
            } catch (\Exception $e) {
                echo 'can not find rest.ini by default!';
                die();
            }
        } else {
            $this->config = $userDefinedConf;
        }
    }

    /**
     * 将 [[/rest/controller>/:id, /rest/<controller>/<action>/:id]]类的请求路由到 <Controller>/<action>
     * 并且使用 Restful 方式
     *
     * @param Request_Abstract $request
     * @param Response_Abstract $response
     * @return bool
     */
    public function routerShutdown(Request_Abstract $request, Response_Abstract $response)
    {
        $this->request = $request;
        $controller = $request->getControllerName();
        $mode = $this->config->get('mode');
        $subdir = '';
        if (is_null($mode))
            $mode = 'subdomain';

        if ($mode === 'subdir')
            $subdir = $this->config->get('subdir');

        if (!empty($subdir) && 0 != strcasecmp($subdir, $controller)) {
            /**
             * 1. 如果使用 subdir 模式，则匹配到才继续
             */
            return true;
        } else {
            /**
             * 2. 如果全局模式，严格执行 [[ /:module/:controller/:action/:id ]] URL 规则匹配
             */
            // 调用 REST 接口, 需关闭页面渲染引擎
            Dispatcher::getInstance()->disableView();
            $this->contentType = $request->getServer('CONTENT_TYPE');
            $this->method = $request->getMethod();
            $this->transferContent();
            /**
             * @var string $actionPrefix Action前缀
             */
            $actionPrefix = $this->config->get($this->method);
            /**
             * 替换链接中 // /// 这类 slash 为 single-slash
             */
            $path = trim(stripslashes($request->getRequestUri()), '/');
            while (strpos($path, '//') !== false) {
                $path = str_replace('//', '/', $path);
            }
            $pathArr = explode('/', $path);
            $this->transferControllerAction($pathArr, $actionPrefix, $mode);
            return true;
        }
    }

    /**
     *
     * @return bool always return true
     */
    public function transferContent()
    {
        if ('OPTIONS' === $this->method) {
            exit();
        } elseif (strpos($this->contentType, 'application/json') === 0) {
            /*json 数据格式*/
            if ($inputs = file_get_contents('php://input')) {
                $inputData = json_decode($inputs, true);
                if ($inputData) {
                    $GLOBALS['_' . $this->method] = $inputData;
                } else {
                    parse_str($inputs, $GLOBALS['_' . $this->method]);
                }
            }
        } elseif ($this->method === 'PUT' && ($inputs = file_get_contents('php://input'))) {
            /*直接解析*/
            parse_str($inputs, $GLOBALS['_PUT']);
        }
        return true;
    }

    /**
     * @param array $pathArr
     * @param string $actionPrefix
     * @param string $mode 模式
     * @return RestPlugin todo 多个参数的情况
     * todo 多个参数的情况
     * @throws \Exception
     */
    public function transferControllerAction($pathArr, $actionPrefix = '', $mode = 'subdomain')
    {
        if ($mode === 'subdir') {
            array_shift($pathArr);
        }
        switch (count($pathArr)) {
            case 1:
                $this->request->setControllerName(ucfirst($pathArr[0]));
                $this->request->setActionName($actionPrefix);
                break;
            case 2:
                //
                // /:controller/:action || /subdir/:controller/:action
                // /:controller/:id

                if (is_numeric($pathArr[1])) {
                    $this->request->setParam($this->config->get('param'), intval($pathArr[1]));
                    $this->request->setControllerName(ucfirst($pathArr[0]));
                    $this->request->setActionName($actionPrefix);
                } else {
                    $this->request->setControllerName(ucfirst($pathArr[0]));
                    $this->request->setActionName($actionPrefix . '_' . $pathArr[1]);
                }
                break;
            case 3:
                // /subdir/:controller/:action/:id || /:controller/:action/:id || /:controller/:id/:action
                if (is_numeric($pathArr[2])) {
                    $this->request->setParam($this->config->get('param'), intval($pathArr[2]));
                    $this->request->setActionName($actionPrefix . '_' . $pathArr[1]);
                    $this->request->setControllerName(ucfirst($pathArr[0]));
                } else {
                    $this->request->setActionName($actionPrefix . '_' . $pathArr[1] . '_' . $pathArr[2]);
                    $this->request->setControllerName(ucfirst($pathArr[0]));
                }
                break;
//            case 4:
//                if (is_numeric($pathArr[3])) {
//                    $this->request->setParam($this->config->get('param'), intval($pathArr[3]));
//                    $this->request->setActionName($actionPrefix . '_' . $pathArr[2]);
//                    $this->request->setControllerName(ucfirst($pathArr[1]));
//                } else {
//                    $this->request->setActionName($actionPrefix . '_' . $pathArr[2] . '_' . $pathArr[3]);
//                    $this->request->setControllerName(ucfirst($pathArr[1]));
//                }
//                break;
            default:
                throw new \Exception('Unsupported uri structure: ' . $this->request->getRequestUri());
                break;
        }
        return $this;
    }
}
