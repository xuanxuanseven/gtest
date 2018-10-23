<?php
use Lephp\Lephp;
use Lephp\Core\Input;
use Lephp\Core\Exception;
use Lephp\Core\Log;
use Lephp\Core\Output;

define('APP_NAME','application');
define('APP_PATH',  realpath(dirname(__FILE__) . '/../'));
define('APPLICATION_PATH',APP_PATH);
define('APPLICATION_INI',APP_PATH . "/conf/application.ini");
require_once(APPLICATION_PATH . '/vendor/autoload.php');
require_once(APPLICATION_PATH . '/application/extends/Mine/Slide.php');

try {
    date_default_timezone_set('PRC');
    header("Content-Type:text/html;charset=utf-8");
    Lephp::run();
}catch( Exception $exception ) {
    /**
     * 输出错误页面或者对接口返回错误信息
     */
    $message        =   '[' . date('Y-m-d H:i:s') . '] ' . get_class($exception) . ':' . $exception->getMessage() . 'in ' . $exception->getFile() . ' on line ' . $exception->getLine();
    Log::write($message,'error');
    /**
     * 如果是ajax 或者post提交的数据，发生错误的时候输出json或者jsonp，其他的跳转到星球页；
     */
    if(  IS_AJAX || IS_POST ) {
        $code           =   $exception->getCode();
        $code           =   $code == 0 ? 500 : $code;
        $callback       =   Input::getQuery('callback');
        $outputFormat   =   !empty($callback) ? 'jsonp' : 'json';
        Output::send(['code' => $code, 'message' => 'system error!'], $outputFormat, $callback);
        return false;
    }
}