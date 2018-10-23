<?php
/**
 * Created for LeEco User Center
 * User: Wei Zhu<zhuwei1@le.com>
 * Date: 4/30/16
 * Time: 3:40 PM
 * @copyright LeEco
 * @since 1.0.0
 */

namespace L10N;


class GettextCachedFileReader extends GettextStringReader
{
    public function __construct($filename)
    {
        parent::__construct();
        $this->str = file_get_contents($filename);
        if (false === $this->str)
            return false;
        $this->pos = 0;
    }
}