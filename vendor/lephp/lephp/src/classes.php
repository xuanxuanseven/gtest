<?php
/**
 * Created for LeEco User Center
 * User: Wei Zhu<zhuwei1@le.com>
 * Date: 6/13/16
 * Time: 10:28 AM
 * @copyright LeEco
 * @since 1.0.0
 */
$lephpDir = dirname(__FILE__);
$vendorDir = dirname($lephpDir);

return [
    'Lephp\\'                             => $lephpDir,
    'Lephp\\PluginInjectPlugin'           => $lephpDir . '/PluginInject.php',
    'Lephp\\BaseLephp'                    => $lephpDir . '/BaseLephp.php',
    'Lephp\\Core\\Error'                  => $lephpDir . '/Core/Error.php',
    'Lephp\\Core\\Application'            => $lephpDir . '/Core/Application.php',
    'Lephp\\Core\\Cookie'                 => $lephpDir . '/Core/Cookie.php',
    'Lephp\\Core\\InvalidCallException'   => $lephpDir . '/Core/InvalidCallException.php',
    'Lephp\\Core\\InvalidConfigException' => $lephpDir . '/Core/InvalidConfigException.php',
    'Lephp\\Core'                         => $lephpDir . '/Core',
    'Lephp\\Core\\Controllers'            => $lephpDir . '/Core/Controllers.php',
    'Lephp\\Log'                          => $lephpDir . '/Log',
    'Lephp\\Queue'                        => $lephpDir . '/Queue',
    'Lephp\\Rest'                         => $lephpDir . '/Rest',
    'Lephp\\Helpers'                      => $lephpDir . '/Helpers',
    'Lephp\\Helpers\\Http'                => $lephpDir . '/Helpers/Http',
    'Lephp\\Helpers\\Kafka'               => $lephpDir . '/Helpers/Kafka'
];