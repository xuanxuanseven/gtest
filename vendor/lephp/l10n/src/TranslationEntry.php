<?php
/**
 * Created for LeEco User Center
 * User: Wei Zhu<zhuwei1@le.com>
 * Date: 4/28/16
 * Time: 10:14 AM
 * @copyright LeEco
 * @since 1.0.0
 */

namespace L10N;

class TranslationEntry
{
    /**
     * 是否有复数形式
     * @var bool
     */
    public $isPlural = false;
    /**
     * Gettext Context
     * @var null
     */
    public $context = null;
    /**
     *
     * @var null
     */
    public $singular = null;
    /**
     * @var null
     */
    public $plural = null;
    /**
     * @var array
     */
    public $translations = [];
    /**
     * @var string
     */
    public $translationComments = '';
    /**
     * @var string
     */
    public $extractedComments = '';
    /**
     * @var array
     */
    public $references = [];
    /**
     * @var array
     */
    public $flags = [];

    public function __construct($args = [])
    {
        // if no singular -- empty object
        if (!isset($args['singular'])) {
            return;
        }
        // get member variable values from args hash
        foreach ($args as $varname => $value) {
            $this->$varname = $value;
        }
        if (isset($args['plural'])) $this->isPlural = true;
        if (!is_array($this->translations)) $this->translations = [];
        if (!is_array($this->references)) $this->references = [];
        if (!is_array($this->flags)) $this->flags = [];
    }

    /**
     * Generates a unique key for this entry
     *
     * @return string|bool the key or false if the entry is empty
     */
    public function key()
    {
        if (is_null($this->singular)) return false;
        // prepend context and EOT, like in MO files
        return is_null($this->context) ? $this->singular : $this->context . chr(4) . $this->singular;
    }

    public function mergeWith(&$other)
    {
        $this->flags = array_unique(array_merge($this->flags, $other->flags));
        $this->references = array_unique(array_merge($this->references, $other->references));
        if ($this->extractedComments != $other->extractedComments) {
            $this->extractedComments .= $other->extractedComments;
        }

    }
}