<?php
/**
 * Created for LeEco User Center
 * User: Yishu Gong<gongyishu@le.com>
 * Date: 2016/5/10
 * Time: 7:39
 * @copyright LeEco
 * @since 1.0.0
 */

namespace Lephp\Db\Builder;

use Lephp\Db\Builder;

/**
 * mysql数据库驱动
 */
class Mysql extends Builder
{

    /**
     * 字段和表名处理
     * @access protected
     * @param string $key
     * @return string
     */
    protected function parseKey($key)
    {
        $key = trim($key);
        if (strpos($key, '$.') && false === strpos($key, '(')) {
            // JSON字段支持
            list($field, $name) = explode($key, '$.');
            $key = 'jsn_extract(' . $field . ', \'$.\'.' . $name . ')';
        }
        if (!preg_match('/[,\'\"\*\(\)`.\s]/', $key)) {
            $key = '`' . $key . '`';
        }
        return $key;
    }

    /**
     * 随机排序
     * @access protected
     * @return string
     */
    protected function parseRand()
    {
        return 'rand()';
    }

}