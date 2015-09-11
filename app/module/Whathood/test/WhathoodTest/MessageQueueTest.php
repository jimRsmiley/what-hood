<?php

namespace WhathoodTest;

use PHPUnit_Framework_TestCase as TestCase;
use SlmQueue\Queue\QueuePluginManager;
use SlmQueue\Strategy\MaxRunsStrategy;
use SlmQueue\Strategy\ProcessQueueStrategy;
use SlmQueue\Worker\WorkerEvent;
use SlmQueueTest\Asset\FailingJob;
use SlmQueueTest\Asset\SimpleController;
use SlmQueueTest\Asset\SimpleJob;
use SlmQueueTest\Asset\SimpleQueue;
use SlmQueueTest\Asset\SimpleWorker;
use Zend\Mvc\Router\RouteMatch;
use Zend\ServiceManager\Config;
use SlmQueueDoctrine\Worker\DoctrineWorker;
use Zend\EventManager\EventManager;


class MessageQueueTest extends TestCase {

    /**
     * @var QueuePluginManager
     */
    protected $queuePluginManager;

    /**
     * @var SimpleController
     */
    protected $controller;

    public function setUp() {
        $worker = new DoctrineWorker(new EventManager());
        $worker->getEventManager()->attachAggregate(new ProcessQueueStrategy());
        $worker->getEventManager()->attachAggregate(new MaxRunsStrategy(['max_runs' => 1]));
        $config = new Config([
            'factories' => [
                'message_queue' => 'SlmQueueDoctrine\Factory\DoctrineQueueFactory'
            ],
        ]);
        $this->queuePluginManager = new QueuePluginManager($config);
    }


    public function testeJob()
    {
        /** @var SimpleQueue $queue */
        $queue = $this->queuePluginManager->get('message_queue');
        $queue->push(new SimpleJob());
        $routeMatch = new RouteMatch(['queue' => 'knownQueue']);
        $this->controller->getEvent()->setRouteMatch($routeMatch);
        $result = $this->controller->processAction();
        $this->assertContains("Finished worker for queue 'knownQueue'", $result);
        $this->assertContains("maximum of 1 jobs processed", $result);
    }

}

