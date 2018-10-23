<?php

namespace Lephp\Core;

class Db
{
    //  数据库连接实例
    private static $instances = [];
    //  当前数据库连接实例
    private static $instance = null;
    // 查询次数
    public static $queryTimes = 0;
    // 执行次数
    public static $executeTimes = 0;

    /**
     * 创建数据库连接
     *
     * @param string $app 应用名称
     * @param string $name 数据库名称
     * @param string $type 数据库类型
     * @param string $location 数据库位置
     * @param string $role 主从身份(master 或 slave)
     * @return Object 数据库连接
     * @throws Exception
     *
     *
     * application.ini 配置格式:
     *
     * 类型.应用.名称.区域.角色.配置项 = "配置内容"
     *
     * type.app.name.location.role.dsn = ""
     * type.app.name.location.role.username = ""
     * type.app.name.location.role.password = ""
     *
     *
     * 国内成长体系策略层 MySql 主库例子:
     *
     * mysql.usergrowth.odp.chn.master.dsn = "mysql:host=127.0.0.1;port=3306;dbname=usergrowth"
     * mysql.usergrowth.odp.chn.master.username = "root"
     * mysql.usergrowth.odp.chn.master.password = ""
     */
    public static function connect($app, $name, $type = 'mysql', $location = 'chn', $role = 'master')
    {
        if (!$app) {
            throw new Exception('db app error');
        }
        if (!$name) {
            throw new Exception('db name error');
        }

        $typeConfig = \Yaf\Application::app()->getConfig()->get($type);
        if (!$typeConfig) {
            throw new Exception('db type error');
        }

        $appConfig = $typeConfig->get($app);
        if (!$appConfig) {
            throw new Exception('db app error');
        }

        $nameConfig = $appConfig->get($name);
        if (!$nameConfig) {
            throw new Exception('db config error');
        }

        $locationConfig = $nameConfig->get($location);
        if (!$locationConfig) {
            throw new Exception('db location error');
        }

        $roleConfig = $locationConfig->get($role);
        if (!$roleConfig) {
            throw new Exception('db role error');
        }

        $dsn = $roleConfig->dsn;
        $username = $roleConfig->username;
        $password = $roleConfig->password;

        $config = [
            'type' => $type,
            'dsn' => $dsn,
            'username' => $username,
            'password' => $password,
        ];

        $md5 = md5(serialize($config));
        if (!isset(self::$instances[$md5])) {
            $class = (!empty($config['namespace']) ? $config['namespace'] : '\\Lephp\\Db\\Connector\\') . ucwords($config['type']);
            self::$instances[$md5] = new $class($config);
        }

        self::$instance = self::$instances[$md5];
        return self::$instance;
    }

    // 调用驱动类的方法
    public static function __callStatic($method, $params)
    {
        if (is_null(self::$instance)) {
            // 自动初始化数据库
            self::connect();
        }
        return call_user_func_array([
            self::$instance,
            $method
        ], $params);
    }
}
