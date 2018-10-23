<?php
/**
 * Created for LeEco User Center
 * User: Wei Zhu<zhuwei1@le.com>
 * Date: 4/30/16
 * Time: 3:36 PM
 * @copyright LeEco
 * @since 1.0.0
 */

namespace L10N;


class GettextStringReader extends GettextReader
{
    public $str = '';
    public $pos = 0;

    public function __construct($str = '')
    {
        parent::__construct();
        $this->pos = 0;
        $this->str = $str;
    }

    public function read($bytes)
    {
        $data = $this->subStr($this->str, $this->pos, $bytes);
        $this->pos += $bytes;
        if ($this->strLen($this->str) < $this->pos) $this->pos = $this->strLen($this->str);
        return $data;
    }

    function seekTo($pos) {
        $this->pos = $pos;
        if ($this->strLen($this->str) < $this->pos) $this->pos = $this->strLen($this->str);
        return $this->pos;
    }

    function length() {
        return $this->strLen($this->str);
    }

    function readAll()
    {
        return $this->subStr($this->str, $this->pos, $this->strLen($this->str));
    }
}