<?php
/**
 * Created for LeEco User Center
 * User: Boswell Young<yangbo9@le.com>
 * Date: 16/4/28
 * Time: 下午4:17
 * @copyright LeEco
 * @since 1.0.0
 */

namespace Lephp\Cache\Driver;

use Lephp\Core\Cache;

class Memcached
{
    protected $handler = null;
    protected $options = [
        'host'    => '127.0.0.1',
        'port'    => 11211,
        'expire'  => 0,
        'timeout' => 0,
        // 超时时间（单位：毫秒）
        'length'  => 0,
        'prefix'  => '',
    ];

    /**
     * 架构函数
     * @param array $options 缓存参数
     * @access public
     */
    public function __construct($options = [])
    {
        if (!extension_loaded('memcached')) {
            throw new \Exception('_NOT_SUPPERT_:memcached');
        }
        if (!empty($options)) {
            $this->options = array_merge($this->options, $options);
        }
        $this->handler = new \Memcached;
        // 设置连接超时时间（单位：毫秒）
        if ($this->options['timeout'] > 0) {
            $this->handler->setOption(\Memcached::OPT_CONNECT_TIMEOUT, $this->options['timeout']);
        }
        // 支持集群
        $hosts = explode(',', $this->options['host']);
        $ports = explode(',', $this->options['port']);
        if (empty($ports[0])) {
            $ports[0] = 11211;
        }
        // 建立连接
        $servers = [];
        foreach ((array)$hosts as $i => $host) {
            $servers[] = [
                $host,
                (isset($ports[$i]) ? $ports[$i] : $ports[0]),
                1
            ];
        }
        $this->handler->addServers($servers);
    }

    /**
     * 读取缓存
     * @access public
     * @param string $name 缓存变量名
     * @return mixed
     */
    public function get($name)
    {
        Cache::$readTimes++;
        return $this->handler->get($this->options['prefix'] . $name);
    }

    /**
     * 写入缓存
     * @access public
     * @param string $name 缓存变量名
     * @param mixed $value 存储数据
     * @param integer $expire 有效时间（秒）
     * @return bool
     */
    public function set($name, $value, $expire = null)
    {
        Cache::$writeTimes++;
        if (is_null($expire)) {
            $expire = $this->options['expire'];
        }
        $name = $this->options['prefix'] . $name;
        $expire = 0 == $expire ? 0 : time() + $expire;
        if ($this->handler->set($name, $value, $expire)) {
            if ($this->options['length'] > 0) {
                // 记录缓存队列
                $queue = $this->handler->get('__info__');
                if (!$queue) {
                    $queue = [];
                }
                if (false === array_search($name, $queue)) {
                    array_push($queue, $name);
                }

                if (count($queue) > $this->options['length']) {
                    // 出列
                    $key = array_shift($queue);
                    // 删除缓存
                    $this->handler->delete($key);
                }
                $this->handler->set('__info__', $queue);
            }
            return true;
        }
        return false;
    }

    /**
     * 删除缓存
     * @param    string $name 缓存变量名
     * @param bool|false $ttl
     * @return bool
     */
    public function rm($name, $ttl = false)
    {
        $name = $this->options['prefix'] . $name;
        return false === $ttl ?
            $this->handler->delete($name) :
            $this->handler->delete($name, $ttl);
    }

    /**
     * 清除缓存
     * @access public
     * @return bool
     */
    public function clear()
    {
        return $this->handler->flush();
    }
}
