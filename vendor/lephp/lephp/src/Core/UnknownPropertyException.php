<?php
/**
 * Created by PhpStorm.
 * User: marlin
 * Date: 2016/11/9
 * Time: 下午3:12
 */

namespace Lephp\Core;


/**
 * UnknownPropertyException represents an exception caused by accessing unknown object properties.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class UnknownPropertyException extends Exception
{
    /**
     * @return string the user-friendly name of this exception
     */
    public function getName()
    {
        return 'Unknown Property';
    }
}
