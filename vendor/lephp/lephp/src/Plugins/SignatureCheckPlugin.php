<?php
/**
 * Created by PhpStorm.
 * User: marlin
 * Date: 2016/12/19
 * Time: 下午1:23
 */

namespace Lephp\Plugins;


use Yaf\Plugin_Abstract;
use Yaf\Request_Abstract;
use Yaf\Response_Abstract;

/**
 * 请求签名检查模块
 * Class SignatureCheckPlugin
 * @package Lephp\Plugins
 */
class SignatureCheckPlugin extends Plugin_Abstract
{
    public $checkCallbackFunction;

    public function __construct(callable $checkCallbackFunction)
    {
        $this->checkCallbackFunction = $checkCallbackFunction;
    }

    /**
     * @param Request_Abstract $request
     * @param Response_Abstract $response
     * @return bool|mixed
     */
    public function routerStartup(Request_Abstract $request, Response_Abstract $response)
    {
        if (is_callable($this->checkCallbackFunction)) {
            return call_user_func($this->checkCallbackFunction);
        } else {
            return true;
        }
    }
}