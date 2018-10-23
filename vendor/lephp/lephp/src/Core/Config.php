<?php
/**
 * Created by PhpStorm.
 * User: wanglishuang
 * Date: 2016/6/21
 * Time: 14:35
 */

namespace Lephp\Core;

use Yaf\Conf;
use Yaf\Config\Ini;

class Config
{
    private static $config = array();

    public static function get($source_type, $module, $file_not_exist_throw_exception = false)
    {
        $map_key = $source_type . "_" . $module;
        if (!isset(self::$config[$map_key])) {
        	
            $config_path = APP_PATH . '/conf/' . $source_type . '.ini';
            if (!file_exists($config_path)) {
                if ($file_not_exist_throw_exception) {
                    throw new Exception("config file $source_type $module not exist");
                }
                return array();
            }
            $config = new Ini($config_path, $module);
            if ($config) {
                self::$config[$map_key] = $config->params->toArray();
            } else {
                self::$config[$map_key] = array();
            }
        }
        return self::$config[$map_key];
    }
}