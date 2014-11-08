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

class Module
{
    protected $adminUserIds = array( '1' );
    
    public static $authenticateRoutes = array( 
            array(
                'controller' => 'Whathood\Controller\UserPolygon',
                'action' => 'add' 
                ),
            array(
                'controller' => 'Whathood\Controller\NeighborhoodPolygonVote',
                'action' => 'cast' 
                ),
            );
    
    protected $adminRoutes = array( 
            array(
                'controller' => 'Whathood\Controller\User',
                'action' => 'add' 
                ),
            );
    
    public function onBootstrap(MvcEvent $e)
    {
//        $e->getApplication()->getServiceManager()->get('translator');
        $eventManager        = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);
        
        /*
         * Log exceptions
         */
        /*$eventManager->attach('dispatch.error', function($event){
            $exception = $event->getResult()->exception;
            if ($exception) {
                $sm = $event->getApplication()->getServiceManager();
                $service = $sm->get('Whathood\Service\ErrorHandling');
                $service->logException($exception);
            }
        });*/
        
        $this->checkForAuthentication(
                $eventManager,
                $this->getAuthService($e),
                self::$authenticateRoutes,
                $this->adminRoutes,
                $this->adminUserIds);
    }
    
    public function checkForAuthentication( 
                                    $eventManager,
                                    $auth,
                                    $authRoutes,
                                    $adminRoutes,
                                    $adminIds
    ) {
        $eventManager->attach(
                MvcEvent::EVENT_ROUTE, 
                function($e) use (
                        $auth,
                        $authRoutes, 
                        $adminRoutes,
                        $adminIds ) {
            
            
            
            $match = $e->getRouteMatch();

            // No route match, this is a 404
            if (!$match instanceof \Zend\Mvc\Router\Http\RouteMatch) {
                return;
            }
            
            $inArray = false;
            $authRoutes = array_merge( $authRoutes, $adminRoutes );
            foreach( $authRoutes as $routeName => $params ) {
                $matchRoute = new \Zend\Mvc\Router\Http\RouteMatch( $params );
                
                if( ($match->getParam( 'controller' )) == ($matchRoute->getParam( 'controller' ))
                        && strtolower($match->getParam( 'action' )) == strtolower($matchRoute->getParam( 'action' )) ) {
                    $inArray = true;
                }
            }
            
            /*
             * if the route wasn't in the authenticate-routes, we can proceed
             */
            if (!$inArray) {
                return;
            }

            // User has already authenticated
            if ($auth->hasIdentity()) {
                
                /*
                 * see if the route is an admin route
                 */
                foreach( $adminRoutes as $routeName => $params ) {
                    $matchRoute = new \Zend\Mvc\Router\Http\RouteMatch( $params );


                    if( ($match->getParam( 'controller' )) == ($matchRoute->getParam( 'controller' ))
                            && strtolower($match->getParam( 'action' )) == strtolower($matchRoute->getParam( 'action' )) ) {
                        $inArray = true;
                    }
                }
                
                /*
                 * if not, return
                 */
                if(!$inArray)
                    return;
                else {
                    $userId = $auth->getWhathoodUser()->getId();
                    foreach( $adminIds as $id ) {
                        
                        if( $id == $userId ) {
                            $inArray = true;
                        }
                    }
                    
                    if( !$inArray ) {
                        throw new \Whathood\AuthenticationException("you aren't authorized for this");
                    }
                }
                return;
            }
            
            
            // Redirect to the user login page, as an example
            $url      = '/whathood/auth/login';

            $url .= '?redirect_to='.'http://'.$_SERVER['SERVER_NAME'].$e->getRequest()->getUri()->getPath();
            
            $response = $e->getResponse();
            $response->getHeaders()->addHeaderLine('Location', $url);
            $response->setStatusCode(302);

            return $response;
        }, -100);
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
    
    /*
     * decide whether the current user is an admin
     */
    protected static function isAdminUser() {
        throw new \Excpetion('yet to implement');
    }
    
    public function getAuthService($e) {
        return $e->getApplication()->getServiceManager()
                    ->get('Whathood\Model\AuthenticationService');
    }

}
