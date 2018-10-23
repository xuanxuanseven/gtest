<?php
/**
 * Created for LeEco User Center
 * User: Wei Zhu<zhuwei1@le.com>
 * Date: 6/13/16
 * Time: 3:37 PM
 * @copyright LeEco
 * @since 1.0.0
 */

namespace Lephp\Core;


use Lephp\Lephp;

class Iobject implements Configurable
{
    /**
     * Object constructor.
     * @param array $config
     */
    public function __construct($config = [])
    {
        if (!empty($config))
            Lephp::configure($this, $config);
        $this->init();
    }

    public function init()
    {
    }

    public function __set($name, $value)
    {
    }
}