<?php

namespace WhathoodTest\Controller;

use Zend\Http\Request;
use Zend\Mvc\Router\RouteMatch;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\Router\Http\TreeRouteStack as HttpRouter;
use Zend\Stdlib\Parameters;
use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase,
 ApplicationTest\Bootstrap;

class RoutingTest extends \Whathood\PHPUnit\BaseControllerTest {

    public function setUp() {
        parent::setUp();
    }

    public function testHome() {
        $this->initDb();

        $region = new \Whathood\Entity\Region(array(
            'name' => 'Philadelphia'
        ));
        $this->m()->regionMapper()->save($region);

        $this->getRequest()
                ->setMethod('GET');
        $this->dispatch( '/');

        $this->assertResponseStatusCode(200);
        $this->assertControllerName('whathood\controller\region');
    }
}
?>
