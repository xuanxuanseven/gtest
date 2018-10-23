<?php
namespace Lephp\Queue\RdKafka;

/**
 * RdKafka 封装
 * Class Producer
 * @package Lephp\Queue\RdKafka
 */
class Producer extends \RdKafka\Producer
{
    public function __destruct()
    {
        $this->reset();
    }

    public function reset()
    {
        foreach (get_class_vars('\RdKafka\Producer') as $var) {
            $this->$var = null;
        }
    }
}