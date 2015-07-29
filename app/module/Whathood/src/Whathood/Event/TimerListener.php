<?php

namespace Whathood\Event;

use Zend\EventManager\ListenerAggregateInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\Mvc\MvcEvent;

class TimerListener implements ListenerAggregateInterface
{
    protected $listeners = array();

    protected $_sm = null;

    public function __construct() {
    }

    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach(MvcEvent::EVENT_DISPATCH, array($this, 'startTimer'));
        $this->listeners[] = $events->attach(MvcEvent::EVENT_RENDER, array($this, 'recordTimers'));
    }

    // must implement
    public function detach(EventManagerInterface $events)
    {
        foreach ($this->listeners as $index => $listener) {
            if ($events->detach($listener)) {
                unset($this->listeners[$index]);
            }
        }
    }

    public function startTimer(MvcEvent $event) {
    #    print("stopTimer is happening".$this->getControllerString($event));
    }

    public function recordTimers(MvcEvent $event) {
    #    print("doEvent is happening".$event->getController());
    #    die("and now dying");
    }

    public function getControllerString(MvcEvent $event) {
        return $this->getControllerName($event)."::".$this->getActionName($event);
    }
    public function getControllerName(MvcEvent $event) {
        return $event->getRouteMatch()->getParam('controller');
    }

    public function getActionName(MvcEvent $event) {
        return $event->getRouteMatch()->getParam('action');
    }
}
