<?php
/**
 * HTTP请求
 * Created for LeEco User Center
 * User: Yishu Gong<gongyishu@le.com>
 * Date: 2016/4/27
 * Time: 18:20
 * @copyright LeEco
 * @since 1.0.0
 */

namespace Lephp\Core;


class Http
{
    // 当前的user-agent字符串
    public $uaString = "Mozilla/5.0 (Macintosh; Intel Mac OS X 10.7; rv:14.0) Gecko/20100101 Firefox/14.0.1";

    // 支持的提交方式
    public $postTypeList = [
        "curl",
        "socket",
        "stream"
    ];

    // 本地cookie文件
    private $cookieFile;

    /**
     * 构造函数
     *
     * @param array $params 初始化参数
     */
    public function __construct($params = [])
    {
        if (count($params) > 0) {
            $this->init($params);
        }
    }

    /**
     * 参数初始化
     *
     * @param array $params
     */
    public function init($params)
    {
        if (count($params) > 0) {
            foreach ($params as $key => $val) {
                if (isset($this->$key)) {
                    $this->$key = $val;
                }
            }
        }
    }

    /**
     * 提交请求
     *
     * @param string $url 请求地址
     * @param mixed $data 提交的数据
     * @param string $type 提交类型，curl,socket,stream可选
     * @param int $timeout
     * @return mixed
     * @throws Exception
     */
    public function post($url, $data, $type = "curl", $timeout = 10000)
    {
        Debug::remark('begin');
        if (!in_array($type, $this->postTypeList)) {
            throw new Exception("undefined post type");
        }
        $functionName = $type . "Post";
        $res = call_user_func_array([
                                        $this,
                                        $functionName
                                    ], [
                                        $url,
                                        $data
                                    ]);
        Debug::remark('end');
        $data = is_array($data) ? http_build_query($data) : $data;
        $response = is_array($res) ? json_encode($res) : $res;
        $time = Debug::getRangeTime('begin', 'end') . 's';
        $this->log($time, $url . '?' . $data, $response);
        return $res;
    }


    /**
     * 更改默认的ua信息
     * 本方法常用于模拟各种浏览器
     * @param string $userAgent UA字符串
     * @return $this
     */
    public function setUA($userAgent)
    {
        $this->uaString = $userAgent;

        return $this;
    }

    /**
     * 设置本地cookie文件
     * 在用curl来模拟时常需要设置此项
     * @param $cookieFile
     * @return $this
     */
    public function setCookieFile($cookieFile)
    {
        $this->cookieFile = $cookieFile;

        return $this;
    }

    /**
     * @param $url
     * @param $data
     * @param string $userAgent
     * @param bool $isHttpBuildQuery
     * @param int $timeout
     * @return mixed
     * @throws Exception
     */
    public function curlPost($url, $data, $userAgent = '', $isHttpBuildQuery = true, $timeout = 5, $headers = [])
    {

        if ($userAgent == '') {
            $userAgent = $this->uaString;
        }

        if (!function_exists("curl_init")) {
            throw new Exception('undefined function curl_init');
        }
        if (is_array($data) && $isHttpBuildQuery) {
            $data = http_build_query($data);
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_USERAGENT, $userAgent);

        if (!empty($headers)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }

        $rs = curl_exec($ch);
        curl_close($ch);

        return $rs;
    }

    /**
     *  套接字提交
     * @param $url
     * @param $data
     * @param string $userAgent
     * @param int $port
     * @param int $timeout
     * @return array|string
     * @throws Exception
     */
    public function socketPost($url, $data, $userAgent = '', $port = 80, $timeout = 30)
    {
        $urInfo = parse_url($url);
        $remoteServer = $urInfo['host'];
        $remotePath = $urInfo['path'];
        $socket = fsockopen($remoteServer, $port, $errno, $errstr, $timeout);
        if (!$socket) {
            throw new Exception("$errstr($errno)");
        }

        if ($userAgent == '') {
            $userAgent = $this->uaString;
        }

        if (!is_array($data)) {
            $data = [$data];
        }
        $data = http_build_query($data);
        fwrite($socket, "POST {$remotePath} HTTP/1.0\r\n");
        fwrite($socket, "User-Agent: {$userAgent}\r\n");
        fwrite($socket, "HOST: {$remoteServer}\r\n");
        fwrite($socket, "Content-type: application/x-www-form-urlencoded\r\n");
        fwrite($socket, "Content-length: " . strlen($data) . "\r\n");
        fwrite($socket, "Accept:*/*\r\n");
        fwrite($socket, "\r\n");
        fwrite($socket, "{$data}\r\n");
        fwrite($socket, "\r\n");

        $header = "";
        while ($str = trim(fgets($socket, 4096))) {
            $header .= $str;
        }

        $data = "";
        while (!feof($socket)) {
            $data .= fgets($socket, 4096);
        }

        return $data;
    }

    /**
     * 文件流提交
     *
     * @param string $url 提交地址
     * @param string $data 数据
     * @param string $userAgent 自定义的UA
     *
     * @return mixed
     */
    public function streamPost($url, $data, $userAgent = '')
    {
        if ($userAgent == '') {
            $userAgent = $this->uaString;
        }

        if (!is_array($data)) {
            $data = [$data];
        }

        $data = http_build_query($data);
        $context = [
            'http' => [
                'method'  => 'POST',
                'header'  => 'Content-type: application/x-www-form-urlencoded' . "\r\n" . 'User-Agent : ' . $userAgent . "\r\n" . 'Content-length: ' . strlen($data),
                'content' => $data
            ]
        ];
        $streamContext = stream_context_create($context);
        $data = file_get_contents($url, false, $streamContext);

        return $data;
    }

    /**
     *      * 发送请求
     *
     * 本方法通过curl函数向目标服务器发送请求
     *
     * @param string $url 请求地址
     * @param bool $sendAgent
     * @param array $cookie
     * @param int $timeout
     * @param array $headers
     * @return mixed|string
     */
    public function request($url, $sendAgent = true, $cookie = [], $timeout = 2, $headers = [])
    {
        Debug::remark('begin');
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        if ($sendAgent) {
            curl_setopt($ch, CURLOPT_USERAGENT,
                        !empty($this->uaString) ? $this->uaString : $_SERVER['HTTP_USER_AGENT']);
        }
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        if (isset($this->cookieFile)) {
            curl_setopt($ch, CURLOPT_COOKIEJAR, $this->cookieFile);
        }
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); //不验证证书
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); //不验证证书
        if (!empty($cookie) && is_array($cookie)) {
            $cookieStr = '';
            foreach ($cookie as $key => $value) {
                $cookieStr .= $key . '=' . $value . '; ';
            }
            $cookieStr = substr($cookieStr, 0, strlen($cookieStr) - 2);
            curl_setopt($ch, CURLOPT_COOKIE, $cookieStr);
        }
        if (!empty($headers)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }
        $data = curl_exec($ch);
        curl_close($ch);
        Debug::remark('end');

        $data = json_decode($data,true);

        $data = is_array($data) ? http_build_query($data) : $data;
        $time = Debug::getRangeTime('begin', 'end') * 1000;
        $response = is_array($data) ? json_encode($data) : $data;

        $this->log($time, $url, $response);
        
        return $data;
    }

    public function log($time, $request, $response)
    {
        $message = "{$request} | {$response} | {$time}ms";
        Log::write($message, 'http');
    }

    /**
     * 透明代理
     * @param $site
     * @param $remoteDomain
     * @param $proxyDomain
     * @param bool $outputResponse
     */
    public function proxyPostPass($site, $remoteDomain, $proxyDomain, $outputResponse = false)
    {
        /* Set it true for debugging. */
        $logHeaders = FALSE;

        $request = $_SERVER['REQUEST_URI'];

        $ch = curl_init();

        /* If there was a POST request, then forward that as well.*/
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            curl_setopt($ch, CURLOPT_POST, TRUE);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $_POST);
        }
        curl_setopt($ch, CURLOPT_URL, $site . $request);
        curl_setopt($ch, CURLOPT_HEADER, TRUE);

        if (!function_exists('getallheaders')) {
            function getallheaders()
            {
                $headers = '';
                foreach ($_SERVER as $name => $value) {
                    if (substr($name, 0, 5) == 'HTTP_') {
                        $headers[str_replace(' ', '-',
                                             ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
                    }
                }
                return $headers;
            }
        }
        $headers = getallheaders();

        /* Translate some headers to make the remote party think we actually browsing that site. */
        $extraHeaders = [];
        if (isset($headers['Referer'])) {
            $extraHeaders[] = 'Referer: ' . str_replace($proxyDomain, $remoteDomain, $headers['Referer']);
        }
        if (isset($headers['Origin'])) {
            $extraHeaders[] = 'Origin: ' . str_replace($proxyDomain, $remoteDomain, $headers['Origin']);
        }
        if (isset($headers['Accept'])) {
            $extraHeaders[] = 'Accept: ' . $headers['Accept'];
        }

        if (isset($headers['Ssotk'])) {
            $extraHeaders[] = 'SSOTK: ' . $headers['Ssotk'];
        }
        /* Forward cookie as it came.  */
        curl_setopt($ch, CURLOPT_HTTPHEADER, $extraHeaders);

        if (isset($headers['Cookie'])) {
            curl_setopt($ch, CURLOPT_COOKIE, $headers['Cookie']);
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        $response = curl_exec($ch);
        $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $headers = substr($response, 0, $header_size);
        $body = substr($response, $header_size);
        $headerArray = explode(PHP_EOL, $headers);
        /* Process response headers. */
        foreach ($headerArray as $header) {
            $colonPos = strpos($header, ':');
            if ($colonPos !== FALSE) {
                $headerName = substr($header, 0, $colonPos);
                /* Ignore content headers, let the webserver decide how to deal with the content. */
//                if (trim($headerName) == 'Content-Encoding') continue;
//                if (trim($headerName) == 'Content-Length') continue;
//                if (trim($headerName) == 'Transfer-Encoding') continue;
//                if (trim($headerName) == 'Location') continue;
                /* -- */
                /* Change cookie domain for the proxy */
                if (trim($headerName) == 'Set-Cookie') {
                    $header = str_replace('domain=' . $remoteDomain, 'domain=' . $proxyDomain, $header);
                }
            }
            header($header);
        }
        if ($outputResponse) {
            echo $body;
            exit;
        }
    }
}