<?php
/**
 * 缓存基类
 * Created for LeEco User Center
 * User: Yishu Gong<gongyishu@le.com>
 * Date: 2016/4/27
 * Time: 18:20
 * @copyright LeEco
 * @since 1.0.0
 */

namespace Lephp\Core;

class Cache
{
    protected static $instance = [];
    protected static $_cacheConfig = [];

    /**
     * 操作句柄
     * @var object
     * @access protected
     */
    protected static $handlers = null;

    /**
     * 实例化一个缓存类
     * @param $item
     * @return object
     */
    public static function init($item)
    {
        $cacheConfig = Tool::getConfig('cacheConfig');
        $itemConfig = $cacheConfig[$item];
        if (!empty($itemConfig) && is_array($itemConfig)) {
            self::$_cacheConfig = $itemConfig;
        }
        self::$_cacheConfig['type'] = $itemConfig['type'];
        return self::connect(self::$_cacheConfig);
    }

    /**
     * 连接缓存服务器
     * @param $options
     * @return object
     */
    protected static function connect($options)
    {
        $type = $options['type'];
        $class = '\\Lephp\\Cache\\Driver\\' . ucwords($type);
        unset($options['type']);
        return new $class($options);
    }
}
