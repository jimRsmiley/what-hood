<?php
namespace WhathoodTest\Controller;
 
use WhathoodTest\Bootstrap;
use Whathood\Controller\RegionRestController;
use Zend\Http\Request;
use Zend\Http\Response;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\Router\RouteMatch;
use Zend\Mvc\Router\Http\TreeRouteStack as HttpRouter;
use PHPUnit_Framework_TestCase;
 
class RegionRestControllerTest extends PHPUnit_Framework_TestCase
{
    protected $controller;
    protected $request;
    protected $response;
    protected $routeMatch;
    protected $event;
 
    protected function setUp()
    {
        $serviceManager = Bootstrap::getServiceManager();
        $this->controller = new RegionRestController();
        $this->request    = new Request();
        $this->routeMatch = new RouteMatch(array('controller' => 'index'));
        $this->event      = new MvcEvent();
        $config = $serviceManager->get('Config');
        $routerConfig = isset($config['router']) ? $config['router'] : array();
        $router = HttpRouter::factory($routerConfig);
        $this->event->setRouter($router);
        $this->event->setRouteMatch($this->routeMatch);
        $this->controller->setEvent($this->event);
        $this->controller->setServiceLocator($serviceManager);
    }
 
    public function testGetListCanBeAccessed()
    {
        $result   = $this->controller->dispatch($this->request);
        $response = $this->controller->getResponse();
 
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testGetCanBeAccessed()
    {
        $this->routeMatch->setParam('id', '1');
 
        $result   = $this->controller->dispatch($this->request);
        $response = $this->controller->getResponse();
 
        print get_class( $result ) ;
    \Zend\Debug\Debug::dump( $result );
    exit;
        $this->assertEquals(200, $response->getStatusCode());
    }
}