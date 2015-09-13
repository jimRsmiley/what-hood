<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Whathood;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\Application;
use Zend\ModuleManager\Feature\ConsoleBannerProviderInterface;
use Zend\ModuleManager\Feature\ConsoleUsageProviderInterface;
use Zend\Console\Adapter\AdapterInterface as Console;
use Zend\Session\Config\SessionConfig;
use Zend\Session\Container;
use Zend\Session\SessionManager;

class Module implements ConsoleUsageProviderInterface
{
    public function onBootstrap(MvcEvent $e)
    {
        $eventManager        = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);

        $sharedManager = $eventManager->getSharedManager();

        $eventManager->attach('dispatch.error', function($event) {
            $exception = $event->getResult()->exception;
            if ($exception) {
                $sm = $event->getApplication()->getServiceManager();
                $service = $sm->get('Whathood\ErrorHandling');
                $service->logException($exception);
            }
        });

        //controller not found, invalid, or route is not matched anymore
        $eventManager->attach('dispatch.error',
               array($this,
              'handleControllerNotFoundAndControllerInvalidAndRouteNotFound' ), 100);

        $this->initSession(array(
            'use_cookies' => false,
        ));
    }

    public function handleControllerNotFoundAndControllerInvalidAndRouteNotFound(MvcEvent $e)
    {
        $error  = $e->getError();
        $logger = $e->getApplication()->getServiceManager()->get('Whathood\Logger');
        if ($error == Application::ERROR_CONTROLLER_NOT_FOUND) {
            //there is no controller named $e->getRouteMatch()->getParam('controller')
            $logText =  'The requested controller '
                        .$e->getRouteMatch()->getParam('controller'). '  could not be mapped to an existing controller class.';

            $logger->err($logText);
        }

        else if ($error == Application::ERROR_CONTROLLER_INVALID) {
            //the controller doesn't extends AbstractActionController
            $logText =  'The requested controller '
                        .$e->getRouteMatch()->getParam('controller'). ' is not dispatchable';

            $logger->err($logText);
        }

        else if ($error == Application::ERROR_ROUTER_NO_MATCH) {
            // the url doesn't match route, for example, there is no /foo literal of route
            $logText =  'The requested URL could not be matched by routing.';
            $logger->err($logText);
        }
        else if ($error == Application::ERROR_EXCEPTION) {
            $exception = $e->getParam('exception');

            if ($exception) {
                $sm = $e->getApplication()->getServiceManager();
                $service = $sm->get('Whathood\ErrorHandling');
                $service->logException($exception);
            }
        }
    }

    /**
     * This method is defined in ConsoleUsageProviderInterface
     */
    public function getConsoleUsage(Console $console)
    {
        return array(

            // watcher-route
            array('queue --rebuild-borders', 'rebuild all neighborhood borders'),
            array('--forever','run watcher in a loop forever'),
            array('--force','force a rebuild of whole system'),
            array('--neighbrohood=',"specify the neighborhood name to use, replace white spaces with '+'"),
            array('--region=','specify the region to use'),
            array('--grid-res=','override the default grid resolution'),
            array('--target-precision','override the default target precision'),

            // neighborhood-delete
            'neighborhood delete [--id=] [--neighborhood=] [--region=]' => 'Delete neighborhoods',
            array('--id=','specify the neighborhood id to delete'),
            array('--neighbrohood=',"specify the neighborhood name to delete, replace white spaces with '+'"),
            array('--region=','specify the region to delete'),

            // test-point-route
            'test-point show [--neighborhood=] [--region=] [--grid-resolution=]' => 'generate test points',
            array('--neighborhood','specify the neighborhood name'),
            array('--region','the region name'),
            array('--grid-resolution','the grid resolution'),

            // test-point-route
            'postgres size' => 'print size information about the database',
        );
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\ClassMapAutoloader' => array(
                __DIR__ . '/autoload_classmap.php',
            ),
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }

    public function initSession($config) {
        $sessionConfig = new SessionConfig();
        $sessionConfig->setOptions($config);
        $sessionManager = new SessionManager($sessionConfig);
        $sessionManager->start();
        Container::setDefaultManager($sessionManager);
    }
}
