<?php


namespace Lephp\Core;

class InvalidRouteException extends UserException
{
    /**
     * @return string the user-friendly name of this exception
     */
    public function getName()
    {
        return 'Invalid Route';
    }
}
