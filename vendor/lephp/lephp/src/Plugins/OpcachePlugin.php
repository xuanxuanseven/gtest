<?php
/**
 * Created by PhpStorm.
 * User: marlin
 * Date: 2016/12/19
 * Time: 下午1:11
 */

namespace Lephp\Plugins;


use Yaf\Plugin_Abstract;
use Yaf\Request_Abstract;
use Yaf\Response_Abstract;

/**
 * 操作 opcache
 * Class OpcachePlugin
 * @package Lephp\Plugins
 * TODO 需要提供运行方式
 */
class OpcachePlugin extends Plugin_Abstract
{
    public $request;

    public function getOpcacheStatus()
    {
        return opcache_get_status();
    }

    public function resetOpcache()
    {
        return opcache_reset();
    }

    public function routerShutdown(Request_Abstract $request, Response_Abstract $response)
    {
        $this->request = $request;
        $controller = $request->getControllerName();
        if (0 === strcasecmp('rest', $controller)) {
            //
        }
    }
}