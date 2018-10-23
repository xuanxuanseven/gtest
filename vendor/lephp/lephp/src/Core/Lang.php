<?php
/**
 * Created by PhpStorm.
 * User: kim
 * Date: 2016/4/25
 * Time: 16:19
 */

namespace Lephp\Core;


use L10N\ISO639\ISO639;

class Lang
{
    // 语言参数
    private static $lang = [];
    // 语言作用域
    private static $range = 'zh-cn';

    // 设定语言参数的作用域（语言）
    public static function range($range = '')
    {
        if ('' == $range) {
            return self::$range;
        } else {
            self::$range = $range;
        }
    }

    /**
     * 设置语言定义(不区分大小写)
     * @param string|array $name 语言变量
     * @param string $value 语言值
     * @param string $range 作用域
     * @return mixed
     */
    public static function set($name, $value = null, $range = '')
    {
        $range = $range ?: self::$range;
        // 批量定义
        if (!isset(self::$lang[$range])) {
            self::$lang[$range] = [];
        }
        if (is_array($name)) {
            return self::$lang[$range] = array_change_key_case($name) + self::$lang[$range];
        } else {
            return self::$lang[$range][strtolower($name)] = $value;
        }
    }

    /**
     * 加载语言定义(不区分大小写)
     * @param string $file 语言文件
     * @param string $range 作用域
     * @return mixed
     */
    public static function load($file, $range = '')
    {
        $range = $range ?: self::$range;
        if (!isset(self::$lang[$range])) {
            self::$lang[$range] = [];
        }
        // 批量定义
        if (is_string($file)) {
            $file = [$file];
        }
        $lang = [];
        foreach ($file as $_file) {
            $_lang = is_file($_file) ? include $_file : [];
            $lang = array_change_key_case($_lang) + $lang;
        }
        if (!empty($lang)) {
            self::$lang[$range] = $lang + self::$lang[$range];
        }
        return self::$lang[$range];
    }

    /**
     * 获取语言定义(不区分大小写)
     * @param string|null $name 语言变量
     * @param array $vars 变量替换
     * @param string $range 作用域
     * @return mixed
     */
    public static function get($name = null, $vars = [], $range = '')
    {
        $range = $range ?: self::$range;
        // 空参数返回所有定义
        if (empty($name)) {
            return self::$lang[$range];
        }
        $key = strtolower($name);
        $value = isset(self::$lang[$range][$key]) ? self::$lang[$range][$key] : $name;
        if (is_array($vars) && !empty($vars)) {
            // 支持变量
            $replace = array_keys($vars);
            foreach ($replace as &$v) {
                $v = '{$' . $v . '}';
            }
            $value = str_replace($replace, $vars, $value);
        }
        return $value;
    }

    /**
     * 自动侦测设置获取语言选择
     * 本方法会注册全局变量 $language 作为当前会话语言
     */
    public static function detect()
    {
        // 自动侦测设置获取语言选择
        global $language;
        global $iso639Define;
        $langVar = Tool::getConfig('langVar');
        $langCookieVar = $langVar['cookie'];
        $langDetectVar = $langVar['detect'];
        $langSet = '';
        $detectLanguage = true;
        while ($detectLanguage) {
            //最高优先级，从请求参数中获取语言
            if ($language = self::detectRequestLanguage($langDetectVar, $langCookieVar)) {
                break;
            }
            //第二优先级，从cookie中取上一次语言设置
            if ($language = self::detectCookieSettingLanguage($langCookieVar)) {
                break;
            }
            if ($language = self::defaultLanguage($langCookieVar)) {
                break;
            }
            if ($language = self::detectHttpAcceptLanguage($langCookieVar)) {
                break;
            }
            //FIXME HAHAHAHA
            $language = 'zh-CN';
            break;
        }
        $language = str_replace('_', '-', $language);
        $iso639Define = self::isValidLanguage($language);
        if (!$iso639Define) {

            $language = self::defaultLanguage($langCookieVar);
            // 合法的语言
            self::$range = $language;
        }
    }

    /**
     * @param $langCookieVar
     * @return bool
     */
    public static function defaultLanguage($langCookieVar)
    {
        $l10nConfiguration = Tool::getConfig('application');
        if (isset($l10nConfiguration['l10n']['defaultLanguage'])) {
            $language = strtolower($l10nConfiguration['l10n']['defaultLanguage']);
            Cookie::set($langCookieVar, $language, [
                'expire' => 3600,
                'domain' => '.le.com'
            ]);
            return $language;
        }
        return false;
    }

    /**
     * @param $langCookieVar
     * @return bool
     */
    public static function detectHttpAcceptLanguage($langCookieVar)
    {
        if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            // 自动侦测浏览器语言
            preg_match('/^([a-z\d\-]+)/i', $_SERVER['HTTP_ACCEPT_LANGUAGE'], $matches);
            $language = strtolower($matches[1]);
            Cookie::set($langCookieVar, $language, [
                'expire' => 3600,
                'domain' => '.le.com'
            ]);
            return $language;
        }
        return false;
    }

    /**
     * @param $langCookieVar
     * @return bool
     */
    public static function detectCookieSettingLanguage($langCookieVar)
    {
        if (Cookie::get($langCookieVar)) {
            $language = strtolower(Cookie::get($langCookieVar));
            return $language;
        }
        return false;
    }

    /**
     * @param $langDetectVar
     * @param $langCookieVar
     * @return bool
     */
    public static function detectRequestLanguage($langDetectVar, $langCookieVar)
    {
        if (isset($_GET[$langDetectVar])) {
            $language = strtolower($_GET[$langDetectVar]);
            Cookie::set($langCookieVar, $language, [
                'expire' => 3600,
                'domain' => '.le.com'
            ]);
            return $language;
        }
        return false;
    }

    /**
     *
     */
    public static function detectServerLanguage()
    {
//        $_SERVER['location']
    }

    /**
     * @param $language
     * @return bool
     */
    public static function isValidLanguage($language)
    {
        $iso639 = new ISO639();
        try {
            return $iso639->isValid($language);
        } catch (\OutOfBoundsException $e) {
            return false;
        }
    }
}