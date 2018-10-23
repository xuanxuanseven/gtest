<?php
/**
 * Created by PhpStorm.
 * User: marlin
 * Date: 2016/11/9
 * Time: 下午2:43
 */

namespace Lephp\Core;

/**
 * UnknownMethodException represents an exception caused by accessing an unknown object method.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class UnknownMethodException extends Exception
{
    /**
     * @return string the user-friendly name of this exception
     */
    public function getName()
    {
        return 'Unknown Method';
    }
}