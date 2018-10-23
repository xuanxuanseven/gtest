<?php
/**
 * Created for LeEco User Center
 * User: Yishu Gong<gongyishu@le.com>
 * Date: 2016/5/6
 * Time: 18:38
 * @copyright LeEco
 * @since 1.0.0
 */

namespace Lephp\Cache\Driver;


class Memcache
{
    protected $handler = null;
    protected $options = [
        'host'       => '127.0.0.1',
        'port'       => 11211,
        'expire'     => 0,
        'timeout'    => 0,
        // 超时时间（单位：毫秒）
        'persistent' => true,
        'length'     => 0,
    ];

    /**
     * 架构函数
     * @param array $options 缓存参数
     * @access public
     * @throws \Exception
     */
    public function __construct(array $options)
    {
        if (!extension_loaded('memcache')) {
            throw new \Exception('_NOT_SUPPERT_:memcache');
        }
        if (!empty($options)) {
            $this->options = array_merge($this->options, $options);
        }
        $this->handler = new \Memcache;
//        $options    =   [
//            'ucenter'=>[
//                'timeout'       =>  10,
//                'persistent'    =>  true,
//                'hosts' => [
//                    [
//                        'host'  =>  '127.0.0.1',
//                        'port'  =>  '11211'
//                    ],
//                    [
//                        'host'  =>  '127.0.0.1',
//                        'port'  =>  '11211'
//                    ]
//                ]
//            ],
//        ];
        $timeout = $options['timeout'];
        $persistent = $options['persistent'];
        foreach ($options['hosts'] as $option) {
            $host = $option['host'];
            $port = $option['port'];
            $timeout > 0 ?
                $this->handler->addServer($host, $port, $persistent, 1) :
                $this->handler->addServer($host, $port, $persistent, 1, $timeout);
        }
    }

    /**
     * 读取缓存
     * @access public
     * @param string $name 缓存变量名
     * @return mixed
     */
    public function get($name)
    {
        return $this->handler->get($name);
    }

    /**
     * 批量获取缓存
     * @param $bathName
     * @return array
     */
    public function bathGet($bathName)
    {
        $data = [];
        foreach ($bathName as $item) {
            $data[$item] = $this->handler->get($item);
        }
        return $data;
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
        if (is_null($expire)) {
            $expire = $this->options['expire'];
        }
        if ($this->handler->set($name, $value, 0, $expire)) {
            return true;
        }
        return false;
    }

    /**
     * 批量设置缓存，如果返回数据，里面为错误的key
     *
     * @param $bathValue
     * @return bool
     */
    public function bathSet($bathValue)
    {
        if (!is_array($bathValue) || empty($bathValue)) {
            return false;
        }
        foreach ($bathValue as $item => $value) {
            $values = $value;
            $expire = $this->options['expire'];
            if (is_array($value)) {
                $values = $value[0];
                $expire = $value[1];
            }
            if (!$this->handler->set($item, $values, $expire)) {
                $error[] = $item;
            }
        }
        if (empty($error)) {
            return true;
        }
        return $error;
    }

    /**
     * 删除缓存
     *
     * @param    string $name 缓存变量名
     * @param bool|false $ttl 失效时间，不会立刻删除，$ttl秒后删除
     *
     * @return bool
     */
    public function rm($name, $ttl = false)
    {
        return false === $ttl ?
            $this->handler->delete($name) :
            $this->handler->delete($name, $ttl);
    }

    /**
     * 批量删除，如果返回为数组，则有删除失败的key
     * @param $bathKey
     * @return array|bool
     */
    public function bathRm($bathKey)
    {
        if (!is_array($bathKey) || empty($bathKey)) {
            return false;
        }
        foreach ($bathKey as $item) {
            if (!$this->handler->delete($item)) {
                $error[] = $item;
            }
        }
        if (empty($error)) {
            return true;
        }
        return $error;
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