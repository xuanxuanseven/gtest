<?php

namespace Lephp\Core;

/**
 * InvalidParamException represents an exception caused by invalid parameters passed to a method.
 *
 */
class InvalidParamException extends \BadMethodCallException
{
    /**
     * @return string the user-friendly name of this exception
     */
    public function getName()
    {
        return 'Invalid Parameter';
    }
}
