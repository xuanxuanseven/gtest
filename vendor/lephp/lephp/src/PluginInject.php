<?php
namespace Lephp;

use Lephp\Core\Plugin;
use Yaf\Application;
use Yaf\Dispatcher;
use Yaf\Registry;
use Yaf\Request_Abstract;
use Yaf\Response_Abstract;
use Yaf\Router;

/**
 * Created for LeEco User Center
 * User: Wei Zhu<zhuwei1@le.com>
 * Date: 6/16/16
 * Time: 10:43 AM
 * @copyright LeEco
 * @since 1.0.0
 */
class PluginInjectPlugin extends Plugin
{
    public function routerShutdown(Request_Abstract $request, Response_Abstract $response)
    {
        /** @var Dispatcher $dispatcher */
        $dispatcher = Application::app()->getDispatcher();
        /** @var Router $router */
        $router = $dispatcher->getRouter();
        $currentRoute = $router->getCurrentRoute();
        $config = Registry::get('moduleConfig');
        $ruleArray = explode('_', $currentRoute);
        $urlRuleName = $ruleArray[1];
        $plugins = isset($config['urlManager']['rules'][$urlRuleName]['plugins']) ? $config['urlManager']['rules'][$urlRuleName]['plugins'] : [];
        if (!empty($plugins)) {
            foreach ($plugins as $pluginName => $pluginConfig) {
                if (isset($pluginConfig['callableObject']) && isset($pluginConfig['callableMethod'])) {
                    if (isset($pluginConfig['callablePreInjectMethod'])) {
                        call_user_func([
                                           $pluginConfig['callableObject'],
                                           $pluginConfig['callablePreInjectMethod']
                                       ]);
                    }
                    $readyForInject = Lephp::createObject($pluginConfig);
                    call_user_func([
                                       $pluginConfig['callableObject'],
                                       $pluginConfig['callableMethod']
                                   ], $readyForInject);
                } else {
                    Lephp::createObject($pluginConfig);
                }
            }
        }
    }
}