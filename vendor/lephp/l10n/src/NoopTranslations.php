<?php
/**
 * Created for LeEco User Center
 * User: Wei Zhu<zhuwei1@le.com>
 * Date: 4/28/16
 * Time: 3:34 PM
 * @copyright LeEco
 * @since 1.0.0
 */

namespace L10N;


class NoopTranslations
{
    public $entries = [];
    public $headers = [];

    public function init()
    {
    }

    public function addEntry()
    {
        return true;
    }

    public function addEntryOrMerge()
    {
    }

    public function setHeader($header, $value)
    {
    }

    public function setHeaders($headers)
    {
    }

    public function getHeader()
    {
        return false;
    }

    function translate($singular, $context = null)
    {
        return $singular;
    }

    public function translateEntry(&$entry)
    {
        return false;
    }

    public function translatePlural()
    {
    }

    public function selectPluralForm($count)
    {
        return 1 === $count ? 0 : 1;
    }

    public function getPluralFormsCount()
    {
        return 2;
    }

    public function mergeWith()
    {
    }

    public function mergeOriginalsWith()
    {
    }
}