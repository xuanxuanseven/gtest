<?php

/**
 * Created by PhpStorm.
 * User: kim
 * Date: 2016/4/25
 * Time: 11:16
 */

use Yaf\Bootstrap_Abstract;
use Yaf\Loader;
use Yaf\Dispatcher;
use Yaf\Registry;
use Yaf\Route\Regex;
use Lephp\Lephp;
use Lephp\Web\Application;

class Bootstrap extends Bootstrap_Abstract
{
    /**
     * 初始化配置文件
     */
    public function _initConfig(Dispatcher $dispatcher)
    {
        Lephp::bootstrap($dispatcher);
    }

    public function _initLephp()
    {
        global $lephp;
        $config = Registry::get('moduleConfig');

        /**
         * @global $lephp \Lephp\Web\Application
         */
        $lephp = new Application($config);
    }

    /**
     * 注册插件
     * @param Dispatcher $dispatcher
     */
    public function _initPlugin(Dispatcher $dispatcher)
    {

    }
}
