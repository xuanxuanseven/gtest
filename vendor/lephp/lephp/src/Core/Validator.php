<?php

/**
 * Class     Validator
 * 验证类
 *
 * @author   yangyang3
 */
namespace Lephp\Core;

use Yaf\Registry;

class Validator
{
    /**
     *
     * 验证UID
     *
     * @param $uid
     *
     * @return bool
     */
    public static function isUid($uid)
    {
        if (is_numeric($uid) && 10 === strlen($uid)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     *
     * 验证数组
     *
     * @param $arr
     *
     * @return bool
     */
    public static function isArray($arr)
    {
        if (is_array($arr)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     *
     * 验证电话
     *
     * @param $tel
     *
     * @return bool
     */
    public static function isTel($tel)
    {
        if (preg_match("/^0[0-9]{2,3}[\-][2-9][0-9]{6,7}[\-]?[0-9]?$/", $tel)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     *
     * 验证UID
     *
     * @param $phone
     *
     * @return bool
     */
    public static function isPhone($phone)
    {
        if (preg_match("/^1[0-9]{10}$/", $phone)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param $zip
     *
     * @return bool
     */
    public static function isZip($zip)
    {
        if (preg_match("/^[0-9]d{5}$/", $zip)) {
            return true;
        } else {
            return false;
        }
    }


    /**
     * Method  regex
     * 正则验证方法
     *
     * @author yangyang3
     * @static
     *
     * @param string $variable
     * @param string $pattern
     *
     * @return bool
     */
    public static function isMatchRegex($variable = '', $pattern = '')
    {
        if (empty($pattern)) {
            return true;
        }

        if (preg_match($pattern, $variable)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Method  is_null
     * 空验证方法
     *
     * @author yangyang3
     * @static
     *
     * @param string $variable
     *
     * @return bool
     */
    public static function isNull($variable = '')
    {
        $variable = trim($variable);

        if (empty($variable)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Method  is_not_null
     * 非空验证方法
     *
     * @author yangyang3
     * @static
     *
     * @param string $variable
     *
     * @return bool
     */
    public static function isNotNull($variable = '')
    {
        $variable = trim($variable);

        if (!empty($variable)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Method  is_email
     * EMAIL验证方法
     *
     * @author yangyang3
     * @static
     *
     * @param string $variable
     *
     * @return bool
     */
    public static function isMail($variable = '')
    {
        $pattern = '/^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/is';

        if (preg_match($pattern, trim($variable))) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param string $variable
     * @return bool
     * @author yanqing9
     */
    public static function isMailorphone($variable = '')
    {
        if (self::isMail($variable) || self::isPhone($variable)) {
            return true;
        }
        return false;
    }

    /**
     * @param string $variable
     * @return bool
     * @author yanqing9
     *
     */
    public static function isWeibologinname($variable = '')
    {
        $pattern = '/^[A-Za-z0-9\._\-\@]+$/is';
        if (preg_match($pattern, trim($variable)) && strlen($variable) >= 1 && strlen($variable) <= 64) {
            return true;
        }
        return false;
    }

    /**
     * Method  is_url
     * URL验证方法
     *
     * @author yangyang3
     * @static
     *
     * @param string $variable
     *
     * @return bool
     */
    public static function isUrl($variable = '')
    {
        $pattern = '/^(?:https?):\/\/(?:[a-z0-9]+\-?[a-z0-9]+\.)*([a-z0-9]+(?:\-?[a-z0-9])*\.[a-z]{2,})(?:\/?.*)$/is';

        if (preg_match($pattern, trim($variable))) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Method  is_numeric
     * 数字验证方法
     *
     * @author yangyang3
     * @static
     *
     * @param int|string $variable
     *
     * @return bool
     */
    public static function isNumeric($variable = '')
    {
        if (is_numeric(trim($variable))) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Method  is_equals
     * 相等验证方法
     *
     * @author yangyang3
     * @static
     *
     * @param string $variable1
     * @param string $variable2
     *
     * @return bool
     */
    public static function isEquals($variable1 = '', $variable2 = '')
    {
        if ((string)$variable1 === (string)$variable2) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Method  is_GreaterThan
     * 大于验证方法
     *
     * @author weisong3
     * @static
     *
     * @param string $variable1
     * @param string $variable2
     *
     * @return bool
     */
    public static function isGreaterThan($variable1 = '', $variable2 = '')
    {
        if ((string)$variable1 > (string)$variable2) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Method  is_LessThan
     * 小于验证方法
     *
     * @author weisong3
     * @static
     *
     * @param string $variable1
     * @param string $variable2
     *
     * @return bool
     */
    public static function isLessThan($variable1 = '', $variable2 = '')
    {
        if ((string)$variable1 < (string)$variable2) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Method  is_GreaterThanOrEqualTo
     * 大于等于验证方法
     *
     * @author weisong3
     * @static
     *
     * @param string $variable1
     * @param string $variable2
     *
     * @return bool
     */
    public static function isGreaterThanOrEqualTo($variable1 = '', $variable2 = '')
    {
        if ((string)$variable1 >= (string)$variable2) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Method  is_LessThanOrEqualTo
     * 小于等于验证方法
     *
     * @author weisong3
     * @static
     *
     * @param string $variable1
     * @param string $variable2
     *
     * @return bool
     */
    public static function isLessThanOrEqualTo($variable1 = '', $variable2 = '')
    {
        if ((string)$variable1 <= (string)$variable2) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Method  is_not_equals
     * 不相等验证方法
     *
     * @author yangyang3
     * @static
     *
     * @param string $variable1
     * @param string $variable2
     *
     * @return bool
     */
    public static function isNotEquals($variable1 = '', $variable2 = '')
    {
        if ((string)$variable1 !== (string)$variable2) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Method  is_match_length
     * 长度验证方法
     *
     * @author yangyang3
     * @static
     *
     * @param string $variable
     * @param int $length
     *
     * @return bool
     */
    public static function isMatchLength($variable = '', $length = 0)
    {
        if (strlen($variable) === (int)$length) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Method  is_in_string
     * IN验证方法
     *
     * @author yangyang3
     * @static
     *
     * @param string $variable
     * @param string $string
     * @param string $delimiter
     *
     * @return bool
     */
    public static function isInString($variable = '', $string = '', $delimiter = ',')
    {
        if (strpos($string, $delimiter) === false) {
            return false;
        }

        $stringList = explode($delimiter, $string);

        if (empty($stringList)) {
            return false;
        }

        if (in_array($variable, $stringList)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Method  is_in_range
     * 范围验证方法
     *
     * @author yangyang3
     * @static
     *
     * @param int $variable
     * @param string $range
     * @param string $delimiter
     *
     * @return bool
     */
    public static function isInRange($variable = 0, $range = '', $delimiter = '-')
    {
        if (strpos($range, $delimiter) === false) {
            return false;
        }

        $rangeList = explode($delimiter, $range);

        if (count($rangeList) !== 2) {
            return false;
        }

        $variable = trim($variable);

        if ($variable > $rangeList[0] && $variable < $rangeList[1]) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Method  is_date
     * 日期验证方法
     *
     * @author yangyang3
     * @static
     *
     * @param string $variable
     * @param string $dateDelimiter
     *
     * @return bool
     */
    public static function isDate($variable = '', $dateDelimiter = '-')
    {
        $timestamp = strtotime($variable);

        $date = date("Y{$dateDelimiter}m{$dateDelimiter}d", $timestamp);

        if ($variable === $date) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Method  is_datetime
     * 时间验证方法
     *
     * @author yangyang3
     * @static
     *
     * @param string $variable
     * @param string $dateDelimiter
     * @param string $time_delimiter
     *
     * @return bool
     */
    public static function isDateTime($variable = '', $dateDelimiter = '-', $time_delimiter = ':')
    {
        $timestamp = strtotime($variable);

        $datetime = date("Y{$dateDelimiter}m{$dateDelimiter}d H{$time_delimiter}i{$time_delimiter}s", $timestamp);

        if ($variable === $datetime) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Method  is_lt_today
     * 等于今天验证方法
     *
     * @author yangyang3
     * @static
     *
     * @param string $variable
     *
     * @return bool
     */
    public static function isEqToday($variable)
    {
        $variableDate = date('Ymd', strtotime($variable));

        $todayDate = date('Ymd', Registry::get('request_time'));

        if ((int)$variableDate === (int)$todayDate) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Method  is_lt_today
     * 小于今天验证方法
     *
     * @author yangyang3
     * @static
     *
     * @param string $variable
     *
     * @return bool
     */
    public static function isLtToday($variable)
    {
        $variableDate = date('Ymd', strtotime($variable));

        $todayDate = date('Ymd', Registry::get('request_time'));

        if ((int)$variableDate < (int)$todayDate) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Method  is_gt_today
     * 大于今天验证方法
     *
     * @author yangyang3
     * @static
     *
     * @param string $variable
     *
     * @return bool
     */
    public static function isGtToday($variable)
    {
        $variableDate = date('Ymd', strtotime($variable));

        $todayDate = date('Ymd', Registry::get('request_time'));

        if ((int)$variableDate > (int)$todayDate) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Method  is_le_today
     * 小于等于今天验证方法
     *
     * @author yangyang3
     * @static
     *
     * @param string $variable
     *
     * @return bool
     */
    public static function isLeToday($variable)
    {
        $variableDate = date('Ymd', strtotime($variable));

        $todayDate = date('Ymd', Registry::get('request_time'));

        if ((int)$variableDate <= (int)$todayDate) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Method  is_ge_today
     * 大于等于今天验证方法
     *
     * @author yangyang3
     * @static
     *
     * @param string $variable
     *
     * @return bool
     */
    public static function isGeToday($variable)
    {
        $variableDate = date('Ymd', strtotime($variable));

        $todayDate = date('Ymd', Registry::get('request_time'));

        if ((int)$variableDate >= (int)$todayDate) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Method  is_eq_date
     * 日期相等验证方法
     *
     * @author yangyang3
     * @static
     *
     * @param string $variable1
     * @param string $variable2
     *
     * @return bool
     */
    public static function isEqDate($variable1, $variable2)
    {
        $variableDate1 = date('Ymd', strtotime($variable1));

        $variableDate2 = date('Ymd', strtotime($variable2));

        if ((int)$variableDate1 === (int)$variableDate2) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Method  is_lt_date
     * 小于指定日期验证方法
     *
     * @author yangyang3
     * @static
     *
     * @param string $variable1
     * @param string $variable2
     *
     * @return bool
     */
    public static function isLtDate($variable1, $variable2)
    {
        $variableDate1 = date('Ymd', strtotime($variable1));

        $variableDate2 = date('Ymd', strtotime($variable2));

        if ((int)$variableDate1 < (int)$variableDate2) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Method  is_gt_date
     * 大于指定日期验证方法
     *
     * @author yangyang3
     * @static
     *
     * @param string $variable1
     * @param string $variable2
     *
     * @return bool
     */
    public static function isGtDate($variable1, $variable2)
    {
        $variableDate1 = date('Ymd', strtotime($variable1));

        $variableDate2 = date('Ymd', strtotime($variable2));

        if ((int)$variableDate1 > (int)$variableDate2) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Method  is_le_date
     * 小于等于指定日期验证方法
     *
     * @author yangyang3
     * @static
     *
     * @param string $variable1
     * @param string $variable2
     *
     * @return bool
     */
    public static function isLeDate($variable1, $variable2)
    {
        $variableDate1 = date('Ymd', strtotime($variable1));

        $variableDate2 = date('Ymd', strtotime($variable2));

        if ((int)$variableDate1 <= (int)$variableDate2) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Method  is_ge_date
     * 大于等于指定日期验证方法
     *
     * @author yangyang3
     * @static
     *
     * @param string $variable1
     * @param string $variable2
     *
     * @return bool
     */
    public static function isGeDate($variable1, $variable2)
    {
        $variableDate1 = date('Ymd', strtotime($variable1));

        $variableDate2 = date('Ymd', strtotime($variable2));

        if ((int)$variableDate1 >= (int)$variableDate2) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Method  is_eq_time
     * 等于指定时间验证方法
     *
     * @author yangyang3
     * @static
     *
     * @param string $variable1
     * @param string $variable2
     *
     * @return bool
     */
    public static function isEqTime($variable1, $variable2)
    {
        $variableTime1 = strtotime($variable1);

        $variableTime2 = strtotime($variable2);

        if ((int)$variableTime1 === (int)$variableTime2) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Method  is_lt_time
     * 小于指定时间验证方法
     *
     * @author yangyang3
     * @static
     *
     * @param string $variable1
     * @param string $variable2
     *
     * @return bool
     */
    public static function isLtTime($variable1, $variable2)
    {
        $variableTime1 = strtotime($variable1);

        $variableTime2 = strtotime($variable2);

        if ((int)$variableTime1 < (int)$variableTime2) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Method  is_gt_time
     * 大于指定时间验证方法
     *
     * @author yangyang3
     * @static
     *
     * @param string $variable1
     * @param string $variable2
     *
     * @return bool
     */
    public static function isGtTime($variable1, $variable2)
    {
        $variableTime1 = strtotime($variable1);

        $variableTime2 = strtotime($variable2);

        if ((int)$variableTime1 > (int)$variableTime2) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Method  is_le_time
     * 小于等于指定时间验证方法
     *
     * @author yangyang3
     * @static
     *
     * @param string $variable1
     * @param string $variable2
     *
     * @return bool
     */
    public static function isLeTime($variable1, $variable2)
    {
        $variableTime1 = strtotime($variable1);

        $variableTime2 = strtotime($variable2);

        if ((int)$variableTime1 <= (int)$variableTime2) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Method  is_ge_time
     * 大于等于指定时间验证方法
     *
     * @author yangyang3
     * @static
     *
     * @param string $variable1
     * @param string $variable2
     *
     * @return bool
     */
    public static function isGeTime($variable1, $variable2)
    {
        $variableTime1 = strtotime($variable1);

        $variableTime2 = strtotime($variable2);

        if ((int)$variableTime1 >= (int)$variableTime2) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 验证是否是 iso639 支持的语言参数格式
     * @param $variable
     * @return bool
     */
    public static function isLanguage($variable)
    {
        if (1 === preg_match('/^\w{2}([-_]\w{2})?$/', $variable)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 验证是否是 iso3166 内支持的国家简写，支持两位或三位简写
     * @param $variable
     * @return bool
     */
    public static function isCountry($variable)
    {
        if (1 === preg_match('/^\w{2,3}$/', $variable)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Method  callback
     * 回调验证方法
     *
     * @author yangyang3
     * @static
     *
     * @param string $variable
     * @param string $functionName
     *
     * @return bool
     */
    public static function callBack($variable = '', $functionName = '')
    {
        if (!function_exists($functionName)) {
            return false;
        }

        if ($functionName($variable)) {
            return true;
        } else {
            return false;
        }
    }
}