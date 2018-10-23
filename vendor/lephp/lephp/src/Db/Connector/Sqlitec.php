<?php
/**
 * sqlite操作类
 * Created by PhpStorm.
 * User: wanglishuang
 * Date: 2017/2/4
 * Time: 16:27
 */
namespace Lephp\Db\Connector;

//use Lephp\Core\Exception;
use SQLite3;


class Sqlitec
{
    //连接sqlite句柄
    private $sqlite3_handle = null;

    /**
     * 要操作的黑白名单名称
     * 库名是$key.db,表明是$key
     * @var string
     */
    private $key = '';

    /**
     * @param string $sqlite_db sqlite3 数据库名称
     * @throws Exception
     */
    public function __construct($sqlite_db) {

        $this->key = $sqlite_db;
        try {
            $this->sqlite3_handle = new SQLite3($sqlite_db);
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
     * 创建table
     * @param string $table 表名。
     */
    public function create($table) {
        $this->sqlite3_handle->exec("DROP TABLE IF EXISTS `{$table}`");

        $sql = "CREATE TABLE `{$table}` (
  `value` varchar(255) NOT NULL default '',
  `begin_time` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `expire_time` timestamp default 0,
  `extra` text,
  PRIMARY KEY (`value`) )";

        echo $sql."\n";
        if(false === $this->sqlite3_handle->exec($sql)) {
            throw new Exception($this->sqlite3_handle->lastErrorMsg(), $this->sqlite3_handle->lastErrorCode());
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

    /**
     * 向名单中插入value
     * @param string $value 黑白名单值
     * @param string $bt 生效时间
     * @param string $et 失效时间，0为永久生效
     * @param string $extra 扩展信息，以json格式存储
     *
     * @throws Exception 若插入数据失败
     */
    public function insert($value, $table, $bt="", $et=0, $extra="") {
        if(!$bt) {
            $bt = date("Y-m-d H:i:s");
        }
        $sql = "INSERT INTO {$table}(`value`, `begin_time`, `expire_time`, `extra`) 
                  VALUES('{$value}', '{$bt}', '{$et}', '{$extra}')";

        if(false === $this->sqlite3_handle->query($sql)) {
            throw new Exception($this->sqlite3_handle->lastErrorMsg(), $this->sqlite3_handle->lastErrorCode());
        }

        return true;
    }

    /**
     * todo 因为目前 sqlite 数据库都是用作读取配置。故暂时不实现更新。需要时请实现它。
     */
    public function update() {

    }

    /**
     * todo 因为目前 sqlite 数据库都是用作读取配置。故暂时不实现删除。需要时请实现它。
     */
    public function delete() {

    }

    public function begin() {
        if(false === $this->db->query("BEGIN;")) {
            throw new Exception($this->sqlite3_handle->lastErrorMsg(), $this->sqlite3_handle->lastErrorCode());
        }
    }
    public function commit() {
        if(false === $this->db->query("COMMIT;")) {
            throw new Exception($this->sqlite3_handle->lastErrorMsg(), $this->sqlite3_handle->lastErrorCode());
        }
    }
}