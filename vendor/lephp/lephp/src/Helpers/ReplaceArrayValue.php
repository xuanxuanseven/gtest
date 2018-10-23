<?php

namespace Lephp\Helpers;

class ReplaceArrayValue
{
    /**
     * @var mixed value used as replacement.
     */
    public $value;

    /**
     * Constructor.
     * @param mixed $value value used as replacement.
     */
    public function __construct($value)
    {
        $this->value = $value;
    }
}
