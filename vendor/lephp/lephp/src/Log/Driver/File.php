<?php

/**
 * Created for LeEco User Center
 * User: Yishu Gong<gongyishu@le.com>
 * Date: 2016/5/10
 * Time: 8:16
 * @copyright LeEco
 * @since 1.0.0
 */

/**
 * 本地化调试输出到文件
 */
namespace Lephp\Log\Driver;

use Lephp\Lephp;
use Lephp\Log\LoggerPlugin;

class File
{
    protected $config = [
        'time_format' => ' c ',
        'file_size'   => 2097152,
        'path'        => LOG_PATH,
    ];

    // 实例化并传入参数
    public function __construct($config = [])
    {
        if (is_array($config)) {
            $this->config = array_merge($this->config, $config);
        }
    }

    /**
     * 日志写入接口
     * @access public
     * @param array $log 日志信息
     * @return bool
     * @deprecated
     *
     */
    public function save(array $log = [])
    {
        foreach ($log as $line) {
            switch ($line['type']){
                case 'error':
                    LoggerPlugin::getInstance()->error($line['msg']);
                    break;
                case 'info':
                    LoggerPlugin::getInstance()->info($line['msg']);
                    break;
                case 'http':
                    Lephp::$app->responseLogStack['api'][] = $line['msg'];
                    break;
                default:
                    LoggerPlugin::getInstance()->info($line['msg']);
                    break;
            }
        }
        return true;
    }

}
