<?php
namespace Lephp\Core;
class Output
{

    public static function toArray($response)
    {
        if ($response !== null) {
            if (is_object($response) && method_exists($response, 'toArray')) {
                $response = $response->toArray();
            } elseif ($response instanceof \Traversable) {
                $temp = [];
                foreach ($response as $key => $val) {
                    $temp[$key] = $val;
                }
                $response = $temp;
            }
        } else {
            $response = [];
        }

        return $response;

    }

    /**
     * 响应输出
     *
     * @access public
     *
     * @param mixed $response
     * @param string $format
     *
     * @return void
     */
    public static function send($response, $format = 'json', $callback = '')
    {
        switch ($format) {
            case 'info':
//                $response = $response;
                break;
            case 'jsonp':
                $response = $callback . '(' . json_encode($response) . ');';
                break;
            case 'json':
            default:
                $response = json_encode($response);
                break;
        }

        if (is_array($response)) {
            $response = json_encode($response);
        }
        echo $response;
    }

}