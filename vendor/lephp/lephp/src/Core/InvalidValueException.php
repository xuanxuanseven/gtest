<?php
namespace Lephp\Core;
/**
 * InvalidValueException represents an exception caused by a function returning a value of unexpected type.
 *
 */
class InvalidValueException extends \UnexpectedValueException
{
    /**
     * @return string the user-friendly name of this exception
     */
    public function getName()
    {
        return 'Invalid Return Value';
    }
}
