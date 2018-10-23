<?php
/**
 * 系统日志类封装
 * Created for LeEco User Center
 * User: Yishu Gong<gongyishu@le.com>
 * Date: 2016/5/10
 * Time: 8:04
 * @copyright LeEco
 * @since 1.0.0
 */


namespace Lephp\Core;

class Log
{
    const LOG = 'log';
    const ERROR = 'error';
    const INFO = 'info';
    const SQL = 'sql';
    const NOTICE = 'notice';
    const ALERT = 'alert';

    // 日志信息
    protected static $log = [];
    // 日志类型
    protected static $type = [
        'log',
        'error',
        'info',
        'sql',
        'notice',
        'alert'
    ];
    // 日志写入驱动
    protected static $driver = null;
    // 通知发送驱动
    protected static $alarm = null;

    // 日志初始化
    public static function init($config = [])
    {
        $type = isset($config['type']) ? $config['type'] : 'File';
        $class = (!empty($config['namespace']) ? $config['namespace'] : '\\Lephp\\Log\\Driver\\') . ucwords($type);
        unset($config['type']);
        self::$driver = new $class($config);
        // 记录初始化信息
        APP_DEBUG && Log::record('[ LOG ] INIT ' . $type . ': ' . var_export($config, true), 'info');
    }

    // 通知初始化
    public static function alarm($config = [])
    {
        $type = isset($config['type']) ? $config['type'] : 'Email';
        $class = (!empty($config['namespace']) ? $config['namespace'] : '\\Lephp\\Log\\Alarm\\') . ucwords($type);
        unset($config['type']);
        self::$alarm = new $class($config['alarm']);
        // 记录初始化信息
        APP_DEBUG && Log::record('[ CACHE ] ALARM ' . $type . ': ' . var_export($config, true), 'info');
    }

    /**
     * 获取全部日志信息
     * @return array
     */
    public static function getLog()
    {
        return self::$log;
    }

    /**
     * 记录调试信息
     * @param mixed $msg 调试信息
     * @param string $type 信息类型
     * @return void
     */
    public static function record($msg, $type = 'log')
    {
        if (!is_string($msg)) {
            $msg = var_export($msg, true);
        }
        self::$log[] = [
            'type' => $type,
            'msg'  => $msg
        ];
    }

    /**
     * 清空日志信息
     * @return void
     */
    public static function clear()
    {
        self::$log = [];
    }

    /**
     * 保存调试信息
     * @return bool
     */
    public static function save()
    {
        if (is_null(self::$driver)) {
            self::init(Tool::getConfig('log'));
        }
        return self::$driver->save(self::$log);
    }

    /**
     * 实时写入日志信息 并支持行为
     * @param mixed $msg 调试信息
     * @param string $type 信息类型
     * @return bool
     * @deprecated 请使用 LogPlugin::getInstance()->info('xxx');
     */
    public static function write($msg, $type = 'log')
    {
        if (!is_string($msg)) {
            $msg = var_export($msg, true);
        }
        if (in_array($type, ['error', 'http', 'log'])) {
            $msg = str_replace("\n", " ", $msg);
        }

        // 封装日志信息
        $log[] = [
            'type' => $type,
            'msg'  => $msg
        ];
        if (is_null(self::$driver)) {
            self::init(Tool::getConfig('log'));
        }
        // 写入日志
        return self::$driver->save($log);
    }

    /**
     * 发送预警通知
     * @param mixed $msg 调试信息
     * @return void
     */
    public static function send($msg)
    {
        self::$alarm && self::$alarm->send($msg);
    }

    // 静态调用
    public static function __callStatic($method, $args)
    {
        if (in_array($method, self::$type)) {
            array_push($args, $method);
            return call_user_func_array('Log::record', $args);
        }
    }
}
