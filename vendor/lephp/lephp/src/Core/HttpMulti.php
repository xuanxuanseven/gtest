<?php
/**
 * 并发HTTP请求
 * Created for LeEco User Center
 * User: Yishu Gong<gongyishu@le.com>
 * Date: 2016/4/27
 * Time: 18:20
 * @copyright LeEco
 * @since 1.0.0
 */
namespace Lephp\Core;

class HttpMulti
{
    private static $_instances;
    private $_mh = null;
    private $_arrCh = [];
    private $_timeOut = 10;

    // 当前的user-agent字符串
    public $uaString = "Mozilla/5.0 (Macintosh; Intel Mac OS X 10.7; rv:14.0) Gecko/20100101 Firefox/14.0.1";


    /**
     * 构造函数
     */
    public function __construct()
    {
        $this->_mh = curl_multi_init();
    }

    /**
     * 单例获取对象
     */
    static public function getInstance()
    {
        if (!isset(self::$_instances)) {
            self::$_instances = new HttpMulti();
        }

        return self::$_instances;
    }

    /**
     * 添加请求
     *
     * @param $url
     * @param array $data
     * @param string $type
     * @param null $key
     *
     * @return $this
     */
    public function add($url, $data = [], $type = 'get', $key = null)
    {
        if (isset($this->_arrCh[$key])) {
            curl_multi_remove_handle($this->_mh, $this->_arrCh[$key]);
            curl_close($this->_arrCh[$key]);
            unset($this->_arrCh[$key]);
        }

        if (is_array($data)) {
            $data = http_build_query($data);
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HEADER, 0);

        if ('post' == $type) {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        } elseif ('get' == $type && !empty($data)) {
            $url .= (strpos($url, '?') ? '&' : '?') . $data;
        }

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->_timeOut);
        curl_setopt($ch, CURLOPT_USERAGENT, $this->uaString);
        curl_multi_add_handle($this->_mh, $ch);

        if (is_null($key)) {
            $this->_arrCh[] = $ch;
        } else {
            $this->_arrCh[$key] = $ch;
        }

        return $this;
    }

    /**
     * 执行
     * @return mixed
     */
    public function exec()
    {
        $active = null;
        $data = [];
        Debug::remark('begin');

        do {
            $mrc = curl_multi_exec($this->_mh, $active);
        } while ($mrc == CURLM_CALL_MULTI_PERFORM);

        while ($active && $mrc == CURLM_OK) {
            while (curl_multi_exec($this->_mh, $active) === CURLM_CALL_MULTI_PERFORM) ;
            if (curl_multi_select($this->_mh) != -1) {
                do {
                    $mrc = curl_multi_exec($this->_mh, $active);
                } while ($mrc == CURLM_CALL_MULTI_PERFORM);
            }
        }
        foreach ($this->_arrCh as $k => $ch) {
            $data[$k] = "error";
            if (curl_error($ch) == "") {
                $data[$k] = curl_multi_getcontent($ch);
                $url = curl_getinfo($ch)['url'];
                Debug::remark('end');
                $time = Debug::getRangeTime('begin', 'end') * 1000;
                $response = is_array($data[$k]) ? json_encode($data[$k]) : $data[$k];
            } else {
                $url = curl_getinfo($ch)['url'];
                Debug::remark('end');
                $time = Debug::getRangeTime('begin', 'end') * 1000;
                $response = is_array($data[$k]) ? json_encode($data[$k]) : $data[$k];
            }
            curl_multi_remove_handle($this->_mh, $ch);
            curl_close($ch);
            $this->log($time, $url, $response);
        }
        curl_multi_close($this->_mh);
        $this->_mh = null;
        $this->_arrCh = [];
        return $data;
    }

    public function log($time, $request, $response)
    {
        $message = "{$request} | {$response} | {$time}ms";
        Log::write($message, 'http');
    }
}