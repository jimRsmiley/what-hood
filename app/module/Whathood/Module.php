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
        $sharedManager->attach(
            'Zend\Mvc\Controller\AbstractActionController',
            'dispatch',
            array($this, 'handleControllerCannotDispatchRequest' ),
            101
        );
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

    /**
     * This method is defined in ConsoleUsageProviderInterface
     */
    public function getConsoleUsage(Console $console)
    {
        return array(
            'Build new neighborhood polygons',
            'watcher [--forever] [--force] [--neighborhood=] [--region=]' => 'watch for changes in user polygons',
            array('--forever','run watcher in a loop forever'),
            array('--force','force a rebuild of whole system'),
            array('--neighbrohood=',"specify the neighborhood name to use, replace white spaces with '+'"),
            array('--region=','specify the region to use'),
            'neighborhood delete [--id=] [--neighborhood=] [--region=]',
            array('--id=','specify the neighborhood id to delete'),
            array('--neighbrohood=',"specify the neighborhood name to delete, replace white spaces with '+'"),
            array('--region=','specify the region to delete'),

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
