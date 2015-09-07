<?php

namespace WhathoodTest\Controller;

use Zend\Http\Request;
use Zend\Mvc\Router\RouteMatch;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\Router\Http\TreeRouteStack as HttpRouter;
use Zend\Stdlib\Parameters;
use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase,
 ApplicationTest\Bootstrap;
/**
 * Description of NeighborhoodControllerTest
 *
 * @author Jim Smiley twitter:@jimRsmiley
 */
class RoutingTest extends \Whathood\PHPUnit\BaseControllerTest {

    public function setUp() {
        parent::setUp();
    }

    public function testHome() {
        $this->getRequest()
                ->setMethod('GET');
        $this->dispatch( '/');
        $this->assertResponseStatusCode(200);
        $this->assertControllerName('whathood\controller\region');
    }
}
?>
