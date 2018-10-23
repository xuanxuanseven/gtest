<?php
/**
 * Created for LeEco User Center
 * User: Wei Zhu<zhuwei1@le.com>
 * Date: 4/30/16
 * Time: 3:31 PM
 * @copyright LeEco
 * @since 1.0.0
 */

namespace L10N;


class GettextFileReader extends GettextReader
{
    public $f = null;
    public $pos = 0;

    public function __construct($filename)
    {
        parent::__construct();
        $this->f = fopen($filename, 'rb');
    }

    public function read($bytes)
    {
        return fread($this->f, $bytes);
    }

    public function seekTo($pos)
    {
        if ( -1 == fseek($this->f, $pos, SEEK_SET)) {
            return false;
        }
        $this->pos = $pos;
        return true;
    }

    public function isResource()
    {
        return parent::isResource();
    }

    public function feof()
    {
        return feof($this->f);
    }

    public function close()
    {
        return fclose($this->f);
    }

    public function readAll()
    {
        $all = '';
        while ( !$this->feof() )
            $all .= $this->read(4096);
        return $all;
    }


}