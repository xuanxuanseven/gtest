<?php
namespace Lephp\Plugins\Smarty;

use Lephp\Core\Plugin;
use Yaf\Application;
use Yaf\Dispatcher;
use Yaf\Plugin_Abstract;
use Yaf\Request_Abstract;
use Yaf\Response_Abstract;

/**
 * Created for LeEco User Center
 * User: Yishu Gong<gongyishu@le.com>
 * Date: 6/15/16
 * Time: 7:09 PM
 * @copyright LeEco
 * @since 1.0.0
 */
class SmartyPlugin extends Plugin
{

    /**
     * @param Request_Abstract $request
     * @param Response_Abstract $response
     * @return mixed|void
     */
    public function routerShutdown(Request_Abstract $request, Response_Abstract $response)
    {
        /** @var Dispatcher $dispatcher */
        $dispatcher = Application::app()->getDispatcher();
        $dispatcher->disableView();
        $smartyAdapter = new SmartyAdapter();
        $dispatcher->setView($smartyAdapter);
    }


}