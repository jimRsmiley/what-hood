<?php

namespace Whathood\Event;

use Zend\EventManager\ListenerAggregateInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\Mvc\MvcEvent;

class TimerListener implements ListenerAggregateInterface
{
    protected $listeners = array();

    protected $_timer;

    protected $_logger;

    public function __construct(\Whathood\Logger $logger) {
        $this->_logger = $logger;
    }

    public function logger() { return $this->_logger; }

    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach(MvcEvent::EVENT_ROUTE, array($this, 'startTimer'));
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
        $this->_timer = \Whathood\Timer::start(rand(1,99999999));
    }

    public function recordTimers(MvcEvent $event) {
        if ($this->_timer) {
            $elapsed_string = $this->_timer->elapsedReadableString();
            $this->logger()->info(
                sprintf("%s %s",
                    $this->getControllerString($event), $elapsed_string));
        }
    }

    public function getControllerString(MvcEvent $event) {
        return $this->getControllerName($event)."::".$this->getActionName($event);
    }
    public function getControllerName(MvcEvent $event) {
        if (!$event->getRouteMatch())
            return '[UnknownController]';
        return $event->getRouteMatch()->getParam('controller');
    }

    public function getActionName(MvcEvent $event) {
        if (!$event->getRouteMatch())
            return '[unknownAction]';
        return $event->getRouteMatch()->getParam('action');
    }
}
