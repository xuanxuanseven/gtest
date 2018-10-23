<?php
/**
 * Created for LeEco User Center
 * User: Wei Zhu<zhuwei1@le.com>
 * Date: 4/27/16
 * Time: 11:14 PM
 * @copyright LeEco
 * @since 1.0.0
 */

namespace L10N;


class Translations
{
    public $entries = [];
    public $headers = [];

    public function init()
    {
    }

    /**
     * @param $entry
     * @return bool
     */
    public function addEntry($entry)
    {
        if (is_array($entry)) {
            $entry = new TranslationEntry($entry);
        }
        $key = $entry->key();
        if (false === $key) return false;
        $this->entries[$key] = &$entry;
        return true;
    }

    /**
     * @param $entry
     * @return bool
     */
    public function addEntryOrMerge($entry)
    {
        if (is_array($entry)) {
            $entry = new TranslationEntry($entry);
        }
        $key = $entry->key();
        if (false === $key) return false;
        if (isset($this->entries[$key]))
            $this->entries[$key]->merge_with($entry);
        else
            $this->entries[$key] = &$entry;
        return true;
    }

    /**
     * @param $header
     * @param $value
     */
    public function setHeader($header, $value)
    {
        $this->headers[$header] = $value;
    }

    /**
     * @param $headers
     */
    public function setHeaders($headers)
    {
        foreach ($headers as $header => $value) {
            $this->setHeader($header, $value);
        }
    }

    /**
     * @param $header
     * @return bool|mixed
     */
    public function getHeader($header)
    {
        return isset($this->headers[$header]) ? $this->headers[$header] : false;
    }

    /**
     * @param $singular
     * @param null $context
     * @return mixed
     */
    public function translate($singular, $context = null)
    {
        $entry = new TranslationEntry([
                                          'singular' => $singular,
                                          'context'  => $context
                                      ]);
        $translated = $this->translateEntry($entry);
        return ($translated && !empty($translated->translations)) ? $translated->translations[0] : $singular;
    }

    /**
     * @param $entry
     * @return bool|mixed
     */
    public function translateEntry($entry)
    {
        $key = $entry->key();
        return isset($this->entries[$key]) ? $this->entries[$key] : false;
    }

    /**
     * @param $singular
     * @param $plural
     * @param $count
     * @param null $context
     * @return mixed
     */
    public function translatePlural($singular, $plural, $count, $context = null)
    {
        $entry = new TranslationEntry([
                                          'singular' => $singular,
                                          'plural'   => $plural,
                                          'context'  => $context
                                      ]);
        $translated = $this->translateEntry($entry);
        $index = $this->selectPluralForm($count);
        $total_plural_forms = $this->getPluralFormsCount();
        if ($translated && 0 <= $index && $index < $total_plural_forms &&
            is_array($translated->translations) &&
            isset($translated->translations[$index])
        )
            return $translated->translations[$index];
        else
            return 1 == $count ? $singular : $plural;
    }

    /**
     * @param $count
     * @return int
     */
    public function selectPluralForm($count)
    {
        return 1 == $count ? 0 : 1;
    }

    /**
     * @return int
     */
    public function getPluralFormsCount()
    {
        return 2;
    }

    /**
     * @param $other
     */
    public function mergeWith(&$other)
    {
        foreach ($other->entries as $entry) {
            $this->entries[$entry->key()] = $entry;
        }
    }

    /**
     * @param $other
     */
    public function mergeOriginalsWith(&$other)
    {
        foreach ($other->entries as $entry) {
            if (!isset($this->entries[$entry->key()]))
                $this->entries[$entry->key()] = $entry;
            else
                $this->entries[$entry->key()]->merge_with($entry);
        }
    }

}