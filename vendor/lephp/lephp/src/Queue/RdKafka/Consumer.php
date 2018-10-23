<?php
/**
 * Created by PhpStorm.
 * User: marlin
 * Date: 2016/12/14
 * Time: 下午10:40
 */

namespace Lephp\Queue\RdKafka;


class Consumer extends \RdKafka\Consumer
{
    public function __destruct()
    {
        $this->reset();
    }

    public function reset()
    {
        foreach (get_class_vars('\RdKafka\Consumer') as $var) {
            $this->$var = null;
        }
    }
}