<?php
/**
 * Created for LeEco User Center
 * User: Wei Zhu<zhuwei1@le.com>
 * Date: 4/27/16
 * Time: 6:16 PM
 * @copyright LeEco
 * @since 1.0.0
 */

namespace L10N;

class GettextReader
{
    public $endian = 'little';
    public $post = '';
    public $pos = 0;
    public $isOverloaded = false;
    
    public function __construct()
    {
        $this->isOverloaded = ((ini_get("mbstring.func_overload") & 2) != 0) && function_exists('mb_substr');
        $this->pos = 0;
    }

    /**
     * Sets the endianness of the file.
     *
     * @param $endian string 'big' or 'little'
     */
    public function setEndian($endian)
    {
        $this->endian = $endian;
    }

    /**
     * Reads a 32bit Integer from the Stream
     *
     * @return mixed The integer, corresponding to the next 32 bits from
     *    the stream of false if there are not enough bytes or on error
     */
    public function readInt32()
    {
        $bytes = $this->read(4);
        if (4 != $this->strLen($bytes))
            return false;
        $endianLetter = ('big' == $this->endian) ? 'N' : 'V';
        $int = unpack($endianLetter, $bytes);
        return array_shift($int);
    }

    /**
     * Reads an array of 32-bit Integers from the Stream
     *
     * @param integer $count How many elements should be read
     * @return mixed Array of integers or false if there isn't
     *    enough data or on error
     */
    public function readInt32Array($count)
    {
        $bytes = $this->read(4 * $count);
        if (4 * $count != $this->strLen($bytes))
            return false;
        $endian_letter = ('big' == $this->endian) ? 'N' : 'V';
        return unpack($endian_letter . $count, $bytes);
    }

    /**
     * @param string $string
     * @param int $start
     * @param int $length
     * @return string
     */
    public function subStr($string, $start, $length)
    {
        if ($this->isOverloaded) {
            return mb_substr($string, $start, $length, 'ascii');
        } else {
            return substr($string, $start, $length);
        }
    }

    /**
     * @param string $string
     * @return int
     */
    public function strLen($string)
    {
        if ($this->isOverloaded) {
            return mb_strlen($string, 'ascii');
        } else {
            return strlen($string);
        }
    }

    /**
     * @param string $string
     * @param int $chunk_size
     * @return array
     */
    public function strSplit($string, $chunk_size)
    {
        if (!function_exists('str_split')) {
            $length = $this->strLen($string);
            $out = [];
            for ($i = 0; $i < $length; $i += $chunk_size)
                $out[] = $this->subStr($string, $i, $chunk_size);
            return $out;
        } else {
            return str_split($string, $chunk_size);
        }
    }


    public function pos()
    {
        return $this->pos;
    }

    public function isResource()
    {
        return true;
    }

    public function close()
    {
        return true;
    }

}