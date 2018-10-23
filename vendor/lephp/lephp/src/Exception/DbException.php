<?php
namespace Lephp\Exception;

use Lephp\Core\Exception;

/**
 * Database相关异常处理类
 */
class DbException extends Exception
{
    public function __construct($message, Array $config, $sql, $code = 10500)
    {
        $this->message = $message;
        $this->code = $code;

        $this->setData('Database Status', [
            'Error Code'    => $code,
            'Error Message' => $message,
            'Error SQL'     => $sql
        ]);

        $this->setData('Database Config', $config);
    }


}
