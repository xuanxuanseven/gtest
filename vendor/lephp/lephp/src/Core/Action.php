<?php
/**
 * Created by PhpStorm.
 * User: marlin
 * Date: 2016/12/23
 * Time: 下午5:17
 */

namespace Lephp\Core;

class Action extends Component
{
    /**
     * @var string ID of the action
     */
    public $id;
    /**
     * @var Controllers the controller that owns this action
     */
    public $controller;

    /**
     * Constructor.
     *
     * @param string $id the ID of this action
     * @param Controllers $controller the controller that owns this action
     * @param array $config name-value pairs that will be used to initialize the object properties
     */
    public function __construct($id, $controller, $config = [])
    {
        $this->id = $id;
        $this->controller = $controller;
        parent::__construct($config);
    }
    /**
     * This method is called right before `run()` is executed.
     * You may override this method to do preparation work for the action run.
     * If the method returns false, it will cancel the action.
     *
     * @return bool whether to run the action.
     */
    protected function beforeRun()
    {
        return true;
    }

    /**
     * This method is called right after `run()` is executed.
     * You may override this method to do post-processing work for the action run.
     */
    protected function afterRun()
    {
    }
}