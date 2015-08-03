<?php

namespace Whathood\Event;

use Zend\EventManager\ListenerAggregateInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\Mvc\MvcEvent;

class TimerListener implements ListenerAggregateInterface
{
    protected $listeners = array();

    protected $_timer;

    protected $_start;

    protected $_logger;

    public function __construct(\Whathood\Logger $logger) {
        $this->_logger = $logger;
    }

    public function logger() { return $this->_logger; }

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
        $this->_start = microtime(true);
    }

    public function recordTimers(MvcEvent $event) {
        $this->_end = microtime(true);
        $this->logger()->info($this->getControllerString($event). " ".round(($this->_end-$this->_start)*1000)."ms");
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
