<?php
namespace Lephp\Core;

use Lephp\Plugins\LogPlugin;
use Yaf\Controller_Abstract;

/**
 * Created by PhpStorm.
 * User: marlin
 * Date: 2016/11/17
 * Time: 下午4:35
 */
class ErrorController extends Controller_Abstract
{
    /**
     * 默认 flush 日志
     */
    public function __destruct()
    {
        LogPlugin::getInstance()->flush($this->getRequest(), $this->getResponse());
    }
}