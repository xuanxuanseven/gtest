<?php
/**
 * Created by PhpStorm.
 * User: marlin
 * Date: 2016/12/23
 * Time: 下午5:17
 */

namespace Lephp\Core;


class ActionEvent extends Event
{
    /**
     * @var Action the action currently being executed
     */
    public $action;
    /**
     * @var mixed the action result. Event handlers may modify this property to change the action result.
     */
    public $result;
    /**
     * @var bool whether to continue running the action. Event handlers of
     * [[Controller::EVENT_BEFORE_ACTION]] may set this property to decide whether
     * to continue running the current action.
     */
    public $isValid = true;


    /**
     * Constructor.
     * @param Action $action the action associated with this action event.
     * @param array $config name-value pairs that will be used to initialize the object properties
     */
    public function __construct($action, $config = [])
    {
        $this->action = $action;
        parent::__construct($config);
    }
}