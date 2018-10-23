<?php
namespace Lephp\Core;

use Lephp\Lephp;

/**
 * @property mixed charset
 */
abstract class Application extends Module
{
    /**
     * @event Event an event raised before the application starts to handle a request.
     */
    const EVENT_BEFORE_REQUEST = 'beforeRequest';
    /**
     * @event Event an event raised after the application successfully handles a request (before the response is sent out).
     */
    const EVENT_AFTER_REQUEST = 'afterRequest';
    /**
     * Application state used by [[state]]: application just started.
     */
    const STATE_BEGIN = 0;
    /**
     * Application state used by [[state]]: application is initializing.
     */
    const STATE_INIT = 1;
    /**
     * Application state used by [[state]]: application is triggering [[EVENT_BEFORE_REQUEST]].
     */
    const STATE_BEFORE_REQUEST = 2;
    /**
     * Application state used by [[state]]: application is handling the request.
     */
    const STATE_HANDLING_REQUEST = 3;
    /**
     * Application state used by [[state]]: application is triggering [[EVENT_AFTER_REQUEST]]..
     */
    const STATE_AFTER_REQUEST = 4;
    /**
     * Application state used by [[state]]: application is about to send response.
     */
    const STATE_SENDING_RESPONSE = 5;
    /**
     * Application state used by [[state]]: application has ended.
     */
    const STATE_END = 6;


    public $responseLogStack = [];

    /**
     * @var array the parameters supplied to the requested action.
     */
    public $requestedParams;

    /**
     * @var array list of loaded modules indexed by their class names.
     */
    public $loadedModules = [];
    /**
     * @var string the charset currently used for the application.
     */
    public $charset = 'UTF-8';

    /**
     * Application constructor.
     * @param array $config
     */
    public function __construct($config = [])
    {
        Lephp::$app = $this;
        $this->preInit($config);
        Component::__construct($config);
    }

    /**
     * @param $config
     */
    public function preInit(&$config)
    {
        foreach ($this->coreComponents() as $id => $component) {
            if (!isset($config['components'][$id])) {
                $config['components'][$id] = $component;
            } elseif (is_array($config['components'][$id]) && !isset($config['components'][$id]['class'])) {
                $config['components'][$id]['class'] = $component['class'];
            }
        }
    }

    /**
     * Returns the response component.
     * @return \Lephp\Web\Response the response component
     */
    public function getResponse()
    {
        return $this->get('response');
    }


    public function getRequest()
    {
        return $this->get('request');
    }

    /**
     * Returns the database connection component.
     * @return \Lephp\Db\NewConnection the database connection
     */
    public function getDb()
    {
        return $this->get('db');
    }

    public function coreComponents()
    {
        return [];
    }
}
