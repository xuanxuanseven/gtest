<?php

namespace Lephp\Exception;

use Lephp\Core\Exception;

/**
 * Database相关异常处理类
 */
class NotFoundException extends Exception
{
    /**
     * 系统异常后发送给客户端的HTTP Status
     * @var integer
     */
    protected $httpStatus = 404;

}
