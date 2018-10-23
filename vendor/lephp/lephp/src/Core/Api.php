<?php
/**
 * Created for LeEco User Center
 * User: Wei Zhu<zhuwei1@le.com>
 * Date: 6/8/16
 * Time: 11:11 AM
 * @copyright LeEco
 * @since 1.0.0
 */
namespace Lephp\Core;

class Api extends Controllers
{
    /**
     * 生成签名字符串
     * @param $apiKey
     * @param $apiID
     * @param $data
     * @param string $signType
     * @return string
     */
    public function generateSign($apiKey, $apiID, $data, $signType = 'md5')
    {
        switch ($signType) {
            case 'md5':
                return md5(join('', $data) . $apiKey);
            default:
                return md5(join('', $data) . $apiKey);
        }
    }
}