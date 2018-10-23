<?php

namespace Lephp\Core;

/**
 * 核心异常类
 * 所有系统异常必须继承该类
 */
class Exception extends \Exception
{
    /**
     * 系统异常后发送给客户端的HTTP Status
     * @var integer
     */
    protected $httpStatus = 500;

    /**
     * 保存异常页面显示的额外Debug数据
     * @var array
     */
    protected $data = [];

    /**
     * 设置异常额外的Debug数据
     * 数据将会显示为下面的格式
     *
     * Exception Data
     * --------------------------------------------------
     * Label 1
     *   key1      value1
     *   key2      value2
     * Label 2
     *   key1      value1
     *   key2      value2
     *
     * @param string $label 数据分类，用于异常页面显示
     * @param array $data 需要显示的数据，必须为关联数组
     */
    final protected function setData($label, array $data)
    {
        $this->data[$label] = $data;
    }

    /**
     * 获取异常额外Debug数据
     * 主要用于输出到异常页面便于调试
     * @return array 由setData设置的Debug数据
     */
    final public function getData()
    {
        return $this->data;
    }

    /**
     * 获取要发送给客户端的HTTP Status
     * @return integer HTTP Status
     */
    final public function getHttpStatus()
    {
        return $this->httpStatus;
    }
}
