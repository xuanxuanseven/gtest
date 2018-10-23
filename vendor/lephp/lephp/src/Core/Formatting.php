<?php
/**
 * Created for LeEco User Center
 * User: Wei Zhu<zhuwei1@le.com>
 * Date: 5/3/16
 * Time: 11:21 AM
 * @copyright LeEco
 * @since 1.0.0
 */

namespace Lephp\Core;


class Formatting
{
    /**
     * 将对象转换成数组
     * @param $object
     * @return mixed
     */
    public static function object2Array(&$object)
    {
        $object = json_decode(json_encode($object), true);
        return $object;
    }

    public static function isAssociative($array, $allStrings = true)
    {
        if (!is_array($array) || empty($array)) {
            return false;
        }

        if ($allStrings) {
            foreach ($array as $key => $value) {
                if (!is_string($key)) {
                    return false;
                }
            }
            return true;
        } else {
            foreach ($array as $key => $value) {
                if (is_string($key)) {
                    return true;
                }
            }
            return false;
        }
    }
}