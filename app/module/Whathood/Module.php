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

class Module implements ConsoleUsageProviderInterface
{
    public function onBootstrap(MvcEvent $e)
    {
        $eventManager        = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);

        $sharedManager = $eventManager->getSharedManager();
        // controller can't dispatch request action that passed to the url
        $sharedManager->attach('Zend\Mvc\Controller\AbstractActionController',
            'dispatch',
            array($this, 'handleControllerCannotDispatchRequest' ), 101);
        //controller not found, invalid, or route is not matched anymore
        $eventManager->attach('dispatch.error',
               array($this,
              'handleControllerNotFoundAndControllerInvalidAndRouteNotFound' ), 100);
    }

    public function handleControllerCannotDispatchRequest(MvcEvent $e)
    {
        $action = $e->getRouteMatch()->getParam('action');
        $controller = get_class($e->getTarget());

        // error-controller-cannot-dispatch
        if (! method_exists($e->getTarget(), $action.'Action')) {
            $logText = 'The requested controller '.
                        $controller.' was unable to dispatch the request : '.$action.'Action';
            //you can do logging, redirect, etc here..
            $e->getApplication()->getServiceManager()->get('Whathood\Logger')->err($logText);
        }
    }

    public function handleControllerNotFoundAndControllerInvalidAndRouteNotFound(MvcEvent $e)
    {
        $error  = $e->getError();
        if ($error == Application::ERROR_CONTROLLER_NOT_FOUND) {
            //there is no controller named $e->getRouteMatch()->getParam('controller')
            $logText =  'The requested controller '
                        .$e->getRouteMatch()->getParam('controller'). '  could not be mapped to an existing controller class.';

            $e->getApplication()->getServiceManager()->get('Whathood\Logger')->err($logText);
        }

        if ($error == Application::ERROR_CONTROLLER_INVALID) {
            //the controller doesn't extends AbstractActionController
            $logText =  'The requested controller '
                        .$e->getRouteMatch()->getParam('controller'). ' is not dispatchable';

            $e->getApplication()->getServiceManager()->get('Whathood\Logger')->err($logText);
        }

        if ($error == Application::ERROR_ROUTER_NO_MATCH) {
            // the url doesn't match route, for example, there is no /foo literal of route
            $logText =  'The requested URL could not be matched by routing.';
            $e->getApplication()->getServiceManager()->get('Whathood\Logger')->err($logText);
        }
    }

    /**
     * This method is defined in ConsoleUsageProviderInterface
     */
    public function getConsoleUsage(Console $console)
    {
        return array(

            // watcher-route
            'watcher [--forever] [--force] [--neighborhood=] [--region=] [--grid-res=]' => 'watch for changes in user polygons',
            array('--forever','run watcher in a loop forever'),
            array('--force','force a rebuild of whole system'),
            array('--neighbrohood=',"specify the neighborhood name to use, replace white spaces with '+'"),
            array('--region=','specify the region to use'),
            array('--grid-res=','override the default grid resolution'),

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
        );
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }
}
