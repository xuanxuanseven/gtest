<?php
/**
 * Cookie 操作类
 * Created for LeEco User Center
 * User: Yishu Gong<gongyishu@le.com>
 * Date: 2016/4/27
 * Time: 18:20
 * @copyright LeEco
 * @since 1.0.0
 */

namespace Lephp\Core;


class Cookie
{

    protected static $config = [
        // cookie 名称前缀
        'prefix'   => '',
        // cookie 保存时间
        'expire'   => 0,
        // cookie 保存路径
        'path'     => '/',
        // cookie 有效域名
        'domain'   => '',
        //  cookie 启用安全传输
        'secure'   => false,
        // httponly设置
        'httponly' => '',
    ];

    /**
     * Cookie初始化
     * @param array $config
     * @return void
     */
    public static function init(array $config = [])
    {
        self::$config = array_merge(self::$config, array_change_key_case($config));
        if (!empty(self::$config['httponly'])) {
            ini_set('session.cookie_httponly', 1);
        }
    }

    /**
     * 设置或者获取cookie作用域（前缀）
     * @param string $prefix
     * @return string|void
     */
    public static function prefix($prefix = '')
    {
        if (empty($prefix)) {
            return self::$config['prefix'];
        }
        self::$config['prefix'] = $prefix;
    }

    /**
     * Cookie 设置、获取、删除
     *
     * @param string $name cookie名称
     * @param mixed $value cookie值
     * @param mixed $option 可选参数 可能会是 null|integer|string
     * @return mixed
     */
    public static function set($name, $value = '', $option = null)
    {
        /**
         * @internal param mixed $options cookie参数
         */
        // 参数设置(会覆盖黙认设置)
        if (!is_null($option)) {
            if (is_numeric($option)) {
                $option = ['expire' => $option];
            } elseif (is_string($option)) {
                parse_str($option, $option);
            }

            $config = array_merge(self::$config, array_change_key_case($option));
        } else {
            $config = self::$config;
        }
        $name = $config['prefix'] . $name;
        // 设置cookie
        if (is_array($value)) {
            array_walk_recursive($value, 'self::jsonFormatProtect', 'encode');
            $value = 'think:' . json_encode($value);
        }
        $expire = !empty($config['expire']) ? time() + intval($config['expire']) : 0;
        setcookie($name, $value, $expire, $config['path'], $config['domain'], $config['secure'], $config['httponly']);
        $_COOKIE[$name] = $value;
    }

    /**
     * Cookie获取
     * @param string $name cookie名称
     * @param string $prefix cookie前缀
     * @return mixed
     */
    public static function get($name, $prefix = '')
    {
        $prefix = $prefix ? $prefix : self::$config['prefix'];
        $name = $prefix . $name;
        if (isset($_COOKIE[$name])) {
            $value = $_COOKIE[$name];
            if (0 === strpos($value, 'think:')) {
                $value = substr($value, 6);
                $value = json_decode($value, true);
                array_walk_recursive($value, 'self::jsonFormatProtect', 'decode');
            }
            return $value;
        } else {
            return null;
        }
    }

    /**
     * Cookie删除
     * @param string $name cookie名称
     * @param string $prefix cookie前缀
     * @return mixed
     */
    public static function delete($name, $prefix = '')
    {
        $config = self::$config;
        $prefix = $prefix ? $prefix : $config['prefix'];
        $name = $prefix . $name;
        setcookie($name, '', time() - 3600, $config['path'], $config['domain'], $config['secure'], $config['httponly']);
        // 删除指定cookie
        unset($_COOKIE[$name]);
    }

    /**
     * Cookie清空
     * @param string $prefix cookie前缀
     * @return mixed
     */
    public static function clear($prefix = '')
    {
        // 清除指定前缀的所有cookie
        if (empty($_COOKIE)) {
            return;
        }

        // 要删除的cookie前缀，不指定则删除config设置的指定前缀
        $config = self::$config;
        $prefix = $prefix ? $prefix : $config['prefix'];
        if ($prefix) {
            // 如果前缀为空字符串将不作处理直接返回
            foreach ($_COOKIE as $key => $val) {
                if (0 === strpos($key, $prefix)) {
                    setcookie($key, '', time() - 3600, $config['path'], $config['domain'], $config['secure'],
                              $config['httponly']);
                    unset($_COOKIE[$key]);
                }
            }
        } else {
            unset($_COOKIE);
        }
        return;
    }

    private static function jsonFormatProtect(&$val, $key, $type = 'encode')
    {
        if (!empty($val) && true !== $val) {
            $val = 'decode' == $type ? urldecode($val) : urlencode($val);
        }
    }
}