<?php
/**
 * Created by PhpStorm.
 * User: wanglishuang
 * Date: 2016/6/8
 * Time: 16:19
 */
namespace Lephp\Core;
class Dplugin{
    public function get_name() {
        return get_class($this);
    }
    public function run() {
        $this->cli();
    }

    /**
     * 命令行请求初始化
     */
    protected function cli() {
        $opt    = 't:c:';
        $opts   = getopt($opt);

        if (!isset($opts['c'])) {
            error_log('Please use -c to assign controller.');
            exit(1);
        } else {
            Route::$controller_prefix = 'Cli';
            $_SERVER['PATH_INFO'] = $opts['c'];
        }
    }
}