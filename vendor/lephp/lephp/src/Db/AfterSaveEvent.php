<?php
/**
 * Created by PhpStorm.
 * User: marlin
 * Date: 2016/12/26
 * Time: 上午11:03
 */

namespace Lephp\Db;


use Lephp\Core\Event;

class AfterSaveEvent extends Event
{
    /**
     * @var array The attribute values that had changed and were saved.
     */
    public $changedAttributes;
}