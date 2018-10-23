<?php
/**
 * Created by PhpStorm.
 * User: marlin
 * Date: 2016/12/15
 * Time: 上午10:36
 */
namespace Lephp\Helpers\Http;

use GuzzleHttp\Client as GuzzleHttpClient;
use Lephp\Core\Tool;
use Lephp\Lephp;
use Psr\Http\Message\RequestInterface;

/**
 * GuzzleHttp 客户端二次封装
 *
 * Class Client
 * @package Lephp\Helpers\Http
 */
class Client extends GuzzleHttpClient
{
    /**
     * HTTP REQUEST 封装
     * @param string $method
     * @param string $uri
     * @param array $options
     * @return mixed|\Psr\Http\Message\ResponseInterface
     */
    public function request($method, $uri = '', array $options = [])
    {
        $start_time = Tool::getMilliSecond();
        $response = parent::request($method, $uri, $options);
        $end_time = Tool::getMilliSecond();
        // 记录请求响应时间
        Lephp::$app->responseLogStack['api'][] = implode(' | ', [
            'url'           => $uri,
            'method'        => $method,
            'options'       => json_encode($options),
            'status_code'   => $response->getStatusCode(),
            'response_time' => intval($end_time - $start_time)
        ]);
        return $response;
    }

    /**
     * PSR7 http request 封装
     * @param RequestInterface $request
     * @param array $options
     * @return mixed|\Psr\Http\Message\ResponseInterface
     */
    public function send(RequestInterface $request, array $options = [])
    {
        $start_time = Tool::getMilliSecond();
        $response = parent::send($request, $options);
        $end_time = Tool::getMilliSecond();
        // 记录请求响应时间
        Lephp::$app->responseLogStack['api'][] = implode(' | ', [
            'url'           => $request->getUri()->__toString(),
            'method'        => $request->getMethod(),
            'options'       => json_encode($options),
            'status_code'   => $response->getStatusCode(),
            'response_time' => intval($end_time - $start_time)
        ]);
        return $response;
    }


}