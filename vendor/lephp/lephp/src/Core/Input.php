<?php
/**
 * Created for LeEco User Center
 * User: Yishu Gong<gongyishu@le.com>
 * Date: 2016/4/28
 * Time: 16:12
 * @copyright LeEco
 * @since 1.0.0
 */
namespace Lephp\Core;

use Yaf\Dispatcher;

class Input
{
    /**
     * 获取参数(支持批量获取，如果key是数组，那么返回数组)
     *
     * @param $key
     * @param $default
     *
     * @access protected
     * @return mixed
     */
    public static function getQuery($key = null, $default = null)
    {
        $query = array_merge($_GET, $_POST, $_COOKIE, $_SERVER);
        if (null === $key) {
            return $query;
        }
        $queryData = '';
        if (is_array($key) && !empty($key)) {
            foreach ($key as $value) {
                $queryData[$value] = isset($query[$value]) ? $query[$value] : $default;
            }
        } else {
            $queryData = isset($query[$key]) ? $query[$key] : $default;
        }
        return $queryData;
    }


    /**
     * 获取Post参数
     *
     * @param $key
     * @param $default
     *
     * @access protected
     * @return mixed
     */
    public static function getPost($key = null, $default = null)
    {
        if (null === $key) {
            return $_POST;
        }

        return isset($_POST[$key]) ? $_POST[$key] : $default;
    }

    /**
     * 获取Get参数
     *
     * @param $key
     * @param $default
     *
     * @access protected
     * @return mixed
     */
    public static function getGet($key = null, $default = null)
    {
        if (null === $key) {
            return $_GET;
        }

        return isset($_GET[$key]) ? $_GET[$key] : $default;
    }

    /**
     * 获取cookie
     *
     * @param  $key
     * @param  $default
     *
     * @access protected
     * @return mixed
     */
    public static function getCookie($key = null, $default = null)
    {
        if (null === $key) {
            return $_COOKIE;
        }

        return isset($_COOKIE[$key]) ? $_COOKIE[$key] : $default;
    }

    /**
     * 获取文件域
     *
     * @param  $key
     *
     * @access protected
     * @return mixed
     */
    public static function getFile($key = null)
    {
        if ($key === null) {
            return $_FILES;
        }

        return isset($_FILES[$key]) ? $_FILES[$key] : null;
    }

    public static function getOutputType()
    {
        $request = Dispatcher::getInstance()->getRequest();
        $callback = self::getQuery('callback');
        $outputFormat = !empty($callback) ? 'jsonp' : self::getQuery('_format', 'info');
        if ('jsonp' !== $outputFormat && ($request->isXmlHttpRequest() || $request->isPost() || $outputFormat === 'json')) {
            $outputFormat = 'json';
        }
        return $outputFormat;
    }
}