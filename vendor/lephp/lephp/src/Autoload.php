<?php

/**
 * Created by PhpStorm.
 * User: marlin
 * Date: 2016/11/9
 * Time: 下午5:49
 */
class LephpAutoload
{
    public static $classMap;
    public static $aliases;

    public static function autoload($className)
    {
        $classFile = self::getFileNameByNS($className);
        if (file_exists($classFile)) {
        } elseif (isset(static::$classMap[$className])) {
            $classFile = static::$classMap[$className];
            if ($classFile[0] === '@') {
                $classFile = static::getAlias($classFile);
            }
        } elseif (strpos($className, '\\') !== false) {
            $classFile = static::getAlias('@' . str_replace('\\', '/', $className) . '.php', false);
            if ($classFile === false || !is_file($classFile)) {
                return;
            }
        } else {
            return;
        }
        include($classFile);
    }

    public static function getAlias($alias, $throwException = true)
    {
        if (strncmp($alias, '@', 1)) {
            // not an alias
            return $alias;
        }

        $pos = strpos($alias, '/');
        $root = $pos === false ? $alias : substr($alias, 0, $pos);

        if (isset(static::$aliases[$root])) {
            if (is_string(static::$aliases[$root])) {
                return $pos === false ? static::$aliases[$root] : static::$aliases[$root] . substr($alias, $pos);
            } else {
                foreach (static::$aliases[$root] as $name => $path) {
                    if (strpos($alias . '/', $name . '/') === 0) {
                        return $path . substr($alias, strlen($name));
                    }
                }
            }
        }

        if ($throwException) {
            throw new \Exception("Invalid path alias: $alias");
        } else {
            return false;
        }
    }

    private static function getFileNameByNS($className)
    {
        return dirname(dirname(__FILE__)) . '/' . str_replace('\\', '/', lcfirst($className)) . '.php';
    }
}

spl_autoload_register(['LephpAutoload', 'autoload'], true, true);
LephpAutoload::$classMap = require(__DIR__ . '/classes.php');