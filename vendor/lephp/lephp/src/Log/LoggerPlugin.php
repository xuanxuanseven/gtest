<?php
namespace Lephp\Log;

use Lephp\Core\Request;
use Lephp\Core\Tool;
use Lephp\Lephp;
use Yaf\Application;
use Yaf\Plugin_Abstract;
use Yaf\Request_Abstract;
use Yaf\Response_Abstract;

/**
 * Created by PhpStorm.
 * User: marlin
 * Date: 2016/12/14
 * Time: 下午9:58
 */
class LoggerPlugin extends Plugin_Abstract
{
    const EMERGENCY = 'emergency';
    const ALERT = 'alert';
    const CRITICAL = 'critical';
    const ERROR = 'error';
    const WARNING = 'warning';
    const NOTICE = 'notice';
    const INFO = 'info';
    const DEBUG = 'debug';

    /**
     * @var int 日志栈最大数量
     */
    public static $flushInterval = 1000;

    public $count = 0;

    private static $_instances;

    /**
     * @property static array $logStack
     * 日志栈
     */
    public $logStack;

    /**
     * 获取一个 LogPlugin 单例
     *
     */
    public static function getInstance()
    {
        if (!isset(self::$_instances)) {
            self::$_instances = new LoggerPlugin();
        }

        return self::$_instances;
    }

    /**
     * 清空日志栈
     */
    public function resetLogStack()
    {
        $this->logStack = [];
        $this->count = 0;
    }

    /**
     * @param $log
     * @param string $level
     * @param array $context
     */
    public function logIt($log, $level = self::INFO, $context = [])
    {
        $this->logStack[$level][] = $log;
        $this->count += 1;
    }

    /**
     * 业务异常
     * @param $message
     * @param array $context
     */
    public function error($message, $context = [])
    {
        $this->logIt($message, self::ERROR, $context);
    }

    /**
     * 业务警告
     * @param $message
     * @param array $context
     */
    public function warning($message, $context = [])
    {
        $this->logIt($message, self::WARNING, $context);
    }

    /**
     * 业务记录
     * @param $message
     * @param array $context
     */
    public function info($message, $context = [])
    {
        $this->logIt($message, self::INFO, $context);
    }

    /**
     * 警告
     * @param $message
     * @param array $context
     */
    public function alert($message, $context = [])
    {
        $this->logIt($message, self::ALERT, $context);
    }

    /**
     * 调试信息
     * @param $message
     * @param array $context
     */
    public function debug($message, $context = [])
    {
        $this->logIt($message, self::DEBUG, $context);
    }

    /**
     * 紧急通知
     * @param $message
     * @param array $context
     */
    public function emergency($message, $context = [])
    {
        $this->logIt($message, self::EMERGENCY, $context);
    }

    /**
     * 崩溃
     * @param $message
     * @param array $context
     */
    public function critical($message, $context = [])
    {
        $this->logIt($message, self::CRITICAL, $context);
    }

    /**
     * 异常日志手动 flush 方法
     * [重要] 在请求 module 里需存在 ErrorController->errorAction($exception) ,并在其中手动调用此方法
     * [需求] 确保让 yaf dispatcher catchException
     * @param Request_Abstract $request
     * @param Response_Abstract $response
     * @return bool
     *
     */
    public function flush(Request_Abstract $request, Response_Abstract $response)
    {
        $now = strftime('%Y-%m-%d %H:%M:%S', APP_START_TIME / 1000);
        $date = strftime('%Y%m%d', APP_START_TIME / 1000);
        $dir = rtrim(Application::app()->getConfig()->get('application.log.directory'), '/');
        if (defined('LOG_SUB_DIR')) {
            $dir .= '/' . LOG_SUB_DIR;
        }
        $hostname = gethostname();
        $requestUri = $request->getRequestUri();
        $remote = Tool::getUserIp();
        $server = isset($_SERVER['SERVER_ADDR']) ? $_SERVER['SERVER_ADDR'] : '0.0.0.0';
        $idc = Tool::getServerLocationISO3166();
        $method = $request->getMethod();

        if (!is_dir($dir)) {
            !is_dir($dir) && mkdir($dir, 0755, true);
        }
        $appMessages = [];
        //todo 大小检查
        if (!is_null($this->logStack) && is_array($this->logStack) && $this->count > 0) {
            $fp = fopen($dir . '/app.' . $date . '.log', 'a+');
            stream_set_blocking($fp, 0);
            foreach ($this->logStack as $level => $logs) {
                $count = 0;
                while ($count < count($logs)) {
                    $msg = trim($logs[$count]);
                    $appMessages[] = "[{$level}] | {$now} | {$idc} | {$hostname} | {$remote} | {$method} | {$requestUri} | {$msg}\n";
                    if (flock($fp, LOCK_EX)) {
                        fwrite($fp, $appMessages[count($appMessages) - 1]);
                    }
                    $count++;
                    flock($fp, LOCK_UN);
                }
            }
            fclose($fp);
            $this->resetLogStack();
        }
        // 记录请求处理时间 + 接口调用时间
        // 使用 rsyslog 时请保证 imfile 的 readMode=2
        global $lephpRequestCosts;
        $endTime = Tool::getMilliSecond();
        $totalCost = $endTime - APP_START_TIME;
        $externalCallLogs = "\n";
        foreach ($lephpRequestCosts as $url => $cost) {
            $externalCallLogs .= "\t[api] {$url} | {$cost}\n";
        }
        //todo sql 执行前后记录时间
        $responseLogStack = Lephp::$app->responseLogStack;
        if (is_array($responseLogStack) && count($responseLogStack) > 0) {
            foreach ($responseLogStack as $type => $logList) {
                foreach ($logList as $item) {
                    $externalCallLogs .= "\t[{$type}] | {$item}\n";
                }
            }
        }
        $message = "[info] | {$now} | {$idc} | {$server} | {$remote} | {$method} | {$requestUri} | {$totalCost}ms{$externalCallLogs}";
        $fp = fopen($dir . '/response.' . $date . '.log', 'a+');
        stream_set_blocking($fp, 0);
        if (flock($fp, LOCK_EX)) {
            fwrite($fp, $message);
        }
        flock($fp, LOCK_UN);
        fclose($fp);
        return true;
    }

    public static function writeException($message, $type = 'error')
    {
        $request = new Request();
        $now = strftime('%Y-%m-%d %H:%M:%S', APP_START_TIME / 1000);
        $date = strftime('%Y%m%d', APP_START_TIME / 1000);
        $dir = rtrim(Application::app()->getConfig()->get('application.log.directory'), '/');
        if (defined('LOG_SUB_DIR')) {
            $dir .= '/' . LOG_SUB_DIR;
        }
        $hostname = gethostname();
        $requestUri = $request->getUrl();
        $remote = Tool::getUserIp();
        $idc = Tool::getServerLocationISO3166();
        $method = $request->getMethod();
        if (!is_dir($dir)) {
            !is_dir($dir) && mkdir($dir, 0755, true);
        }
        $fp = fopen($dir . '/app.' . $date . '.log', 'a+');
        stream_set_blocking($fp, 0);
        if (flock($fp, LOCK_EX)) {
            fwrite($fp,
                   "{$type} | {$now} | {$idc} | {$hostname} | {$remote} | {$method} | {$requestUri} | {$message}\n");
        }
        flock($fp, LOCK_UN);
        fclose($fp);
        return true;
    }

    /**
     * @param Request_Abstract $request
     * @param Response_Abstract $response
     * @return bool
     * 在所有 dispatch 结束时调用此方法
     */
    public function dispatchLoopShutdown(Request_Abstract $request, Response_Abstract $response)
    {
        return self::getInstance()->flush($request, $response);
    }

    //阻止用户复制对象实例
    public function __clone()
    {
        trigger_error('Clone is not allow', E_USER_ERROR);
    }
}