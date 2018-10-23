<?php
/**
 * 插件开发基类
 * Created for LeEco User Center
 * User: Yishu Gong<gongyishu@le.com>
 * Date: 2016/4/27
 * Time: 18:20
 * @copyright LeEco
 * @since 1.0.0
 */

namespace Lephp\Core;

use Yaf\Plugin_Abstract;
use Yaf\Request_Abstract;
use Yaf\Response_Abstract;

abstract class Plugin extends Plugin_Abstract
{
    /**
     * (Yaf >= 2.2.9)
     * 在路由之前触发
     *
     * @param Request_Abstract $request 当前请求对象
     * @param Response_Abstract $response 当前响应对象
     *
     * @return mixed
     */
    public function routerStartup(Request_Abstract $request, Response_Abstract $response)
    {
    }

    /**
     * (Yaf >= 2.2.9)
     * 路由结束之后触发
     *
     * @param Request_Abstract $request 当前请求对象
     * @param Response_Abstract $response 当前响应对象
     *
     * @return mixed
     */
    public function routerShutdown(Request_Abstract $request, Response_Abstract $response)
    {
    }

    /**
     * (Yaf >= 2.2.9)
     * 分发循环开始之前被触发
     *
     * @param Request_Abstract $request 当前请求对象
     * @param Response_Abstract $response 当前响应对象
     *
     * @return mixed
     */
    public function dispatchLoopStartup(Request_Abstract $request, Response_Abstract $response)
    {
    }

    /**
     * (Yaf >= 2.2.9)
     * 分发之前触发
     *
     * @param Request_Abstract $request 当前请求对象
     * @param Response_Abstract $response 当前响应对象
     *
     * @return mixed
     */
    public function preDispatch(Request_Abstract $request, Response_Abstract $response)
    {
    }

    /**
     * (Yaf >= 2.2.9)
     * 分发结束之后触发
     *
     * @param Request_Abstract $request 当前请求对象
     * @param Response_Abstract $response 当前响应对象
     *
     * @return mixed
     */
    public function postDispatch(Request_Abstract $request, Response_Abstract $response)
    {
    }

    /**
     * (Yaf >= 2.2.9)
     * dispatchLoopShutdown
     *
     * @param Request_Abstract $request 当前请求对象
     * @param Response_Abstract $response 当前响应对象
     *
     * @return mixed
     */
    public function dispatchLoopShutdown(Request_Abstract $request, Response_Abstract $response)
    {
    }
}