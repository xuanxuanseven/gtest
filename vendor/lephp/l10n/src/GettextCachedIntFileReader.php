<?php
/**
 * Created for LeEco User Center
 * User: Wei Zhu<zhuwei1@le.com>
 * Date: 4/30/16
 * Time: 3:44 PM
 * @copyright LeEco
 * @since 1.0.0
 */

namespace L10N;


class GettextCachedIntFileReader extends GettextCachedFileReader
{
    public function __construct($filename)
    {
        parent::__construct($filename);
    }

}