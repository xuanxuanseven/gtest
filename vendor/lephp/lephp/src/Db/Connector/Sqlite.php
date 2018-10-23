<?php
/**
 * sqlite查询操作类
 * 这个类用于前端服务器，应该只有查询操作，所以该类不提供更新操作方法
 * Created by PhpStorm.
 * User: wanglishuang
 * Date: 2017/2/4
 * Time: 15:02
 */
namespace Lephp\Db\Connector;

use Lephp\Core\Exception;
use SQLite3;

class Sqlite
{
    //连接sqlite句柄
    private $sqlite3_handle = null;

    /**
     * @param string $sqlite_db sqlite3 数据库文件全路径名称
     * @throws Exception
     */
    public function __construct($sqlite_db) {

        try {
            $this->sqlite3_handle = new SQLite3($sqlite_db, SQLITE3_OPEN_READWRITE);
        } catch(Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        }
    }

    /**
     * 关闭连接
     */
    public function __destruct() {

        if($this->sqlite3_handle instanceof SQLite3) {
            $this->sqlite3_handle->close();
        }
    }

    /**
     * 执行查询
     * @param string $table     表名。
     * @param array  $condition 查询条件，key 为条件字段，value 为条件值。
     * @return array
     * @throws Exception
     */
    public function query($table, $condition) {

        $sql = "SELECT * FROM {$table}";

        if(count($condition) > 0) {
            $sql .= ' WHERE ';
            foreach($condition as $name => $value) {
                $sql .= "{$name}=" . (is_string($value) ? "'{$value}'" : $value . "{$value}") . ' AND ';
            }

            $sql = substr($sql, 0, strlen($sql) - 5); // 去掉最后的 ' AND '
        }

        $result = $this->sqlite3_handle->query($sql);

        if($result === false) { // 查询结果为 false 说明操作失败
            throw new Exception($this->sqlite3_handle->lastErrorMsg(), $this->sqlite3_handle->lastErrorCode());
        }

        $result_arr = array();

        while($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $result_arr[] = $row;
        }

        $result->finalize();

        return $result_arr;
    }
}