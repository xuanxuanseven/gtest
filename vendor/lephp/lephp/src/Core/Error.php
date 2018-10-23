<?php
/**
 * Created for LeEco User Center
 * User: Yishu Gong<gongyishu@le.com>
 * Date: 2016/5/3
 * Time: 12:03
 * @copyright LeEco
 * @since 1.0.0
 */

namespace Lephp\Core;

use Lephp\Exception\ErrorException;
use Yaf\Registry;

class Error
{
    /**
     * 默认错误号，错误号不能重复；
     * @var array
     */
    private static $info = [
        'system_error' => [
            'code'    => 10000,
            'message' => 'System Error',
            'data'    => []
        ],
    ];

    /**
     * 初始化错误信息
     * Error constructor.
     */
    public function __construct()
    {
        $errorInfo = Registry::get('errorInfo');
        if (!empty($errorInfo) || is_array($errorInfo)) {
            array_merge(self::$info, $errorInfo);
        }
    }

    /**
     * @param $errorCode
     * @return mixed
     */
    public static function info($errorCode)
    {
        if (empty($errorCode) || !in_array($errorCode, self::$info)) {
            return [
                'code'    => 10000,
                'data'    => [],
                'message' => $errorCode
            ];
        }
        return self::errorL10N($errorCode);
    }

    /**
     * 错误信息多语言
     * @param $errorCode
     * @return array
     */
    private function errorL10N($errorCode)
    {
        $errorInfo = self::$info[$errorCode];
//        $errorMessage = L10N::__($errorInfo['message']);
//        $errorInfo['message'] = $errorMessage;
        return $errorInfo;
    }


    /**
     * 注册异常处理
     * @return void
     */
    public static function register()
    {
        set_error_handler([
                              __CLASS__,
                              'appError'
                          ]);
        set_exception_handler([
                                  __CLASS__,
                                  'appException'
                              ]);
        register_shutdown_function([
                                       __CLASS__,
                                       'appShutdown'
                                   ]);
    }

    /**
     * Exception Handler
     * @param  \Exception $exception
     * @return bool  true-禁止往下传播已处理过的异常
     */
    public static function appException($exception)
    {
        // 收集异常数据
        if (APP_DEBUG) {
            // 调试模式，获取详细的错误信息
            $data = [
                'name'    => get_class($exception),
                'file'    => $exception->getFile(),
                'line'    => $exception->getLine(),
                'message' => $exception->getMessage(),
                'trace'   => $exception->getTrace(),
                'code'    => self::getCode($exception),
                'source'  => self::getSourceCode($exception),
                'datas'   => self::getExtendData($exception),

                'tables' => [
                    'GET Data'              => $_GET,
                    'POST Data'             => $_POST,
                    'Files'                 => $_FILES,
                    'Cookies'               => $_COOKIE,
                    'Session'               => isset($_SESSION) ? $_SESSION : [],
                    'Server/Request Data'   => $_SERVER,
                    'Environment Variables' => $_ENV,
                ],
            ];
            $log = "[{$data['code']}]{$data['message']}[{$data['file']}:{$data['line']}]";
        } else {
            // 部署模式仅显示 Code 和 Message
            $data = [
                'code'    => $exception->getCode(),
                'message' => $exception->getMessage(),
            ];
            $log = "[{$data['code']}]{$data['message']}";
        }

        // 记录异常日志
        Log::record($log, 'error');
        Log::save();

        /* 非API模式下的部署模式，跳转到指定的 Error Page */
        $error_page = Tool::getConfig('error_page');
        if (!(APP_DEBUG || IS_AJAX) && !empty($error_page)) {
            header("Location: {$error_page}");
        } else {
            // 输出错误信息
            self::output($exception, $data);
        }
        // 禁止往下传播已处理过的异常
        return true;
    }

    /**
     * Error Handler
     * @param  integer $errno 错误编号
     * @param  integer $errstr 详细错误信息
     * @param  string $errfile 出错的文件
     * @param  integer $errline 出错行号
     * @param array $errcontext
     * @return bool true-禁止往下传播已处理过的异常
     * @throws Exception
     */
    public static function appError($errno, $errstr, $errfile = null, $errline = 0, array $errcontext = [])
    {
        if ($errno & Tool::getConfig('exception_ignore_type')) {
            // 忽略的异常记录到日志
            Log::record("[{$errno}]{$errstr}[{$errfile}:{$errline}]", 'notice');
        } else {
            // 将错误信息托管至 Exception\ErrorException
            throw new ErrorException($errno, $errstr, $errfile, $errline, $errcontext);
            // 禁止往下传播已处理过的异常
            return true;
        }
    }

    /**
     * Shutdown Handler
     * @return bool true-禁止往下传播已处理过的异常; false-未处理的异常继续传播
     */
    public static function appShutdown()
    {
        // 写入日志
        if ($error = error_get_last()) {
            // 将错误信息托管至ErrorException
            $exception = new ErrorException(
                $error['type'],
                $error['message'],
                $error['file'],
                $error['line']
            );

            /**
             * Shutdown handler 中的异常将不被往下传播
             * 所以，这里我们必须手动传播而不能像 Error handler 中那样 throw
             */
            self::appException($exception);
            // 禁止往下传播已处理过的异常
            return true;
        }
        return false;
    }

    /**
     * 输出异常信息
     * @param  \Exception $exception
     * @param  array $data 异常信息
     * @return void
     */
    public static function output($exception, array $data)
    {
        http_response_code($exception instanceof Exception ? $exception->getHttpStatus() : 500);

        $type = Tool::getConfig('default_return_type');
        if (!APP_DEBUG && !Tool::getConfig('show_error_msg')) {
            // 不显示详细错误信息
            $data['message'] = Tool::getConfig('error_message');
        }
        if (IS_AJAX && 'html' != $type) {
            // 输出异常内容
            Response::send($data, 'json');
        } else {
            extract($data);
            Response::send($data, 'json');
        }
    }

    /**
     * 获取错误编码
     * ErrorException则使用错误级别作为错误编码
     * @param  \Exception $exception
     * @return integer                错误编码
     */
    private static function getCode($exception)
    {
        $code = $exception->getCode();
        if (!$code && $exception instanceof ErrorException) {
            $code = $exception->getSeverity();
        }
        return $code;
    }

    /**
     * 获取出错文件内容
     * 获取错误的前9行和后9行
     * @param  \Exception $exception
     * @return array                 错误文件内容
     */
    private static function getSourceCode($exception)
    {
        // 读取前9行和后9行
        $line = $exception->getLine();
        $first = ($line - 9 > 0) ? $line - 9 : 1;

        try {
            $contents = file($exception->getFile());
            $source = [
                'first'  => $first,
                'source' => array_slice($contents, $first - 1, 19),
            ];
        } catch (Exception $e) {
            $source = [];
        }
        return $source;
    }

    /**
     * 获取异常扩展信息
     * 用于非调试模式html返回类型显示
     * @param  \Exception $exception
     * @return array                 异常类定义的扩展数据
     */
    private static function getExtendData($exception)
    {
        $data = [];
        if ($exception instanceof Exception) {
            $data = $exception->getData();
        }
        return $data;
    }
}