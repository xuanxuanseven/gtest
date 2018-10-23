<?php

namespace Lephp;


use Lephp\Core\InvalidConfigException;
use Lephp\Core\InvalidParamException;
use Lephp\Di\Container;
use Lephp\Plugins\LogPlugin;

/**
 * Created for LeEco User Center
 * User: Wei Zhu<zhuwei1@le.com>
 * Date: 6/13/16
 * Time: 5:19 PM
 * @copyright LeEco
 * @since 1.0.0
 */


if (!defined('APP_PATH')) {
    define('APP_PATH', realpath(dirname(__FILE__) . '/../../../'));
}
if (!defined('APP_NAME')) {
    define('APP_NAME', 'application');
}
if (!defined('APPLICATION_PATH')) {
    define('APPLICATION_PATH', APP_PATH);
}
if (!defined('APPLICATION_INI')) {
    define('APPLICATION_INI', APP_PATH . "/conf/application.ini");
}
if (!defined('APP_START_TIME')) {
    list($t1, $t2) = explode(' ', microtime());
    $startTime = sprintf('%d', (floatval($t1) + floatval($t2)) * 1000);
    define('APP_START_TIME', $startTime);
}
if (!defined('APP_DEBUG')) {
    define('APP_DEBUG', false);
}

if (!defined('IS_AJAX')) {
    define('IS_AJAX',
           (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') ? true : false);
}

// 环境常量
if (!defined('IS_CLI')) {
    // 是否是命令行调用
    define('IS_CLI', PHP_SAPI == 'cli' ? 1 : 0);
}
if (!defined('REQUEST_METHOD')) {
    // 请求 method
    define('REQUEST_METHOD', IS_CLI ? 'GET' : $_SERVER['REQUEST_METHOD']);
}
if (!defined('IS_GET')) {
    // 是否是GET
    define('IS_GET', REQUEST_METHOD == 'GET' ? true : false);
}
if (!defined('IS_POST')) {
    define('IS_POST', REQUEST_METHOD == 'POST' ? true : false);
}
if (!defined('IS_PUT')) {
    define('IS_PUT', REQUEST_METHOD == 'PUT' ? true : false);
}
if (!defined('IS_DELETE')) {
    define('IS_DELETE', REQUEST_METHOD == 'DELETE' ? true : false);

}
if (!defined('LOG_PATH')) {
    // 默认日志目录
    define('LOG_PATH', '/letv/wwwlogs/');
}

class BaseLephp
{
    public static $classMap = [];

    /**
     * @var \Lephp\Core\Application the application instance
     */
    public static $app;

    public static $aliases = [];
    /**
     * @var Container the dependency injection (DI) container used by [[createObject()]].
     * You may use [[Container::set()]] to set up the needed dependencies of classes and
     * their initial property values.
     * @see createObject()
     * @see Container
     */
    public static $container;

    /**
     * Translates a path alias into an actual path.
     *
     * The translation is done according to the following procedure:
     *
     * 1. If the given alias does not start with '@', it is returned back without change;
     * 2. Otherwise, look for the longest registered alias that matches the beginning part
     *    of the given alias. If it exists, replace the matching part of the given alias with
     *    the corresponding registered path.
     * 3. Throw an exception or return false, depending on the `$throwException` parameter.
     *
     * For example, by default '@lephp' is registered as the alias to the lephp framework directory,
     * say '/path/to/lephp'. The alias '@lephp/web' would then be translated into '/path/to/lephp/web'.
     *
     * If you have registered two aliases '@foo' and '@foo/bar'. Then translating '@foo/bar/config'
     * would replace the part '@foo/bar' (instead of '@foo') with the corresponding registered path.
     * This is because the longest alias takes precedence.
     *
     * However, if the alias to be translated is '@foo/barbar/config', then '@foo' will be replaced
     * instead of '@foo/bar', because '/' serves as the boundary character.
     *
     * Note, this method does not check if the returned path exists or not.
     *
     * @param string $alias the alias to be translated.
     * @param boolean $throwException whether to throw an exception if the given alias is invalid.
     * If this is false and an invalid alias is given, false will be returned by this method.
     * @return string|boolean the path corresponding to the alias, false if the root alias is not previously registered.
     * @throws InvalidParamException if the alias is invalid while $throwException is true.
     * @see setAlias()
     */
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
            throw new InvalidParamException("Invalid path alias: $alias");
        } else {
            return false;
        }
    }

    /**
     * Returns the root alias part of a given alias.
     * A root alias is an alias that has been registered via [[setAlias()]] previously.
     * If a given alias matches multiple root aliases, the longest one will be returned.
     * @param string $alias the alias
     * @return string|boolean the root alias, or false if no root alias is found
     */
    public static function getRootAlias($alias)
    {
        $pos = strpos($alias, '/');
        $root = $pos === false ? $alias : substr($alias, 0, $pos);

        if (isset(static::$aliases[$root])) {
            if (is_string(static::$aliases[$root])) {
                return $root;
            } else {
                foreach (static::$aliases[$root] as $name => $path) {
                    if (strpos($alias . '/', $name . '/') === 0) {
                        return $name;
                    }
                }
            }
        }

        return false;
    }

    public static function setAlias($alias, $path)
    {
        if (strncmp($alias, '@', 1)) {
            $alias = '@' . $alias;
        }
        $pos = strpos($alias, '/');
        $root = $pos === false ? $alias : substr($alias, 0, $pos);
        if ($path !== null) {
            $path = strncmp($path, '@', 1) ? rtrim($path, '\\/') : static::getAlias($path);
            if (!isset(static::$aliases[$root])) {
                if ($pos === false) {
                    static::$aliases[$root] = $path;
                } else {
                    static::$aliases[$root] = [$alias => $path];
                }
            } elseif (is_string(static::$aliases[$root])) {
                if ($pos === false) {
                    static::$aliases[$root] = $path;
                } else {
                    static::$aliases[$root] = [
                        $alias => $path,
                        $root  => static::$aliases[$root],
                    ];
                }
            } else {
                static::$aliases[$root][$alias] = $path;
                krsort(static::$aliases[$root]);
            }
        } elseif (isset(static::$aliases[$root])) {
            if (is_array(static::$aliases[$root])) {
                unset(static::$aliases[$root][$alias]);
            } elseif ($pos === false) {
                unset(static::$aliases[$root]);
            }
        }
    }

    /**
     * Creates a new object using the given configuration.
     *
     * You may view this method as an enhanced version of the `new` operator.
     * The method supports creating an object based on a class name, a configuration array or
     * an anonymous function.
     *
     * Below are some usage examples:
     *
     * @param string|array|callable $type the object type. This can be specified in one of the following forms:
     *
     * - a string: representing the class name of the object to be created
     * - a configuration array: the array must contain a `class` element which is treated as the object class,
     *   and the rest of the name-value pairs will be used to initialize the corresponding object properties
     * - a PHP callable: either an anonymous function or an array representing a class method (`[$class or $object, $method]`).
     *   The callable should return a new instance of the object being created.
     *
     * @param array $params the constructor parameters
     * @return object the created object
     * @throws InvalidConfigException if the configuration is invalid.
     * @see \Lephp\Di\Container
     */
    public static function createObject($type, array $params = [])
    {
        if (is_string($type)) {
            return static::$container->get($type, $params);
        } elseif (is_array($type) && isset($type['class'])) {
            $class = $type['class'];
            unset($type['class']);
            return static::$container->get($class, $params, $type);
        } elseif (is_callable($type, true)) {
            return static::$container->invoke($type, $params);
        } elseif (is_array($type)) {
            throw new InvalidConfigException('Object configuration must be an array containing a "class" element.');
        } else {
            throw new InvalidConfigException('Unsupported configuration type: ' . gettype($type));
        }
    }

    /**
     * Configures an object with the initial property values.
     * @param object $object the object to be configured
     * @param array $properties the property initial values given in terms of name-value pairs.
     * @return object the object itself
     */
    public static function configure($object, $properties)
    {
        foreach ($properties as $name => $value) {
            $object->$name = $value;
        }

        return $object;
    }

    /**
     * Returns the public member variables of an object.
     * This method is provided such that we can get the public member variables of an object.
     * It is different from "get_object_vars()" because the latter will return private
     * and protected variables if it is called within the object itself.
     * @param object $object the object to be handled
     * @return array the public member variables of the object
     */
    public static function getObjectVars($object)
    {
        return get_object_vars($object);
    }


    public static function trace($message, $category = 'application')
    {
        LogPlugin::getInstance()->info($category . ' | ' . $message);
        return true;
    }

    public static function error($message, $category = 'application')
    {
        LogPlugin::getInstance()->error($category . ' | ' . $message);
        return true;
    }


    public static function warning($message, $category = 'application')
    {
        LogPlugin::getInstance()->error($category . ' | ' . $message);
        return true;
    }

    public static function info($message, $category = 'application')
    {
        LogPlugin::getInstance()->info($category . ' | ' . $message);
        return true;
    }

    /**
     * 标记 Profiling 开始
     * @param $token
     * @param string $category
     * @return bool
     */
    public static function beginProfile($token, $category = 'application')
    {
        Lephp::$app->responseLogStack[$category][] = $token;
        return true;
    }

    /**
     * 标记 Profiling 结束
     * @param $token
     * @param string $category
     * @return bool
     */
    public static function endProfile($token, $category = 'application')
    {
        Lephp::$app->responseLogStack[$category][] = $token;
        return true;
    }

}