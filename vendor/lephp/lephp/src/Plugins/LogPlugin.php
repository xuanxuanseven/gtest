<?php
/**
 * Created by PhpStorm.
 * User: marlin
 * Date: 2016/11/15
 * Time: 上午10:42
 */

namespace Lephp\Plugins;

use Lephp\Log\LoggerPlugin;

/**
 * Class LogPlugin
 * 日志记录类，在 Yaf 的 dispatcherLoopShutdown 的时候自动 flush 日志
 * @package Lephp\Plugins
 * @usages
 * 用法:
 * 可以通过 LogPlugin::getInstance() 获取单例，
 *
 * - [[LogPlugin::getInstance()->info('msg');]]
 * - [[LogPlugin::getInstance()->error('msg');]]
 * - [[LogPlugin::getInstance()->warning('msg');]]
 *
 * @property Integer $flushInterval 日志栈大小（超过本值会自动执行一次flush)
 * @property Integer $count 日志栈计数器
 * @property array $logStack 日志栈
 */
class LogPlugin extends LoggerPlugin
{
}