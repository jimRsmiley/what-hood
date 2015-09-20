<?php

namespace WhathoodTest\Controller;

use Zend\Http\Request;
use Zend\Mvc\Router\RouteMatch;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\Router\Http\TreeRouteStack as HttpRouter;
use Zend\Stdlib\Parameters;
use Whathood\PHPUnit\BaseControllerTest;
use Whathood\Entity\Neighborhood;
use Whathood\Entity\UserPolygon;

class SearchControllerTest extends BaseControllerTest {

    public function setUp() {
        parent::setUp();
    }

    public function testBaseRouting() {
        $this->initDb();
        $region = new \Whathood\Entity\Region(array(
            'name' => 'WhathoodTest'.rand(0,99999999)
        ));
        $this->m()->regionMapper()->save($region);
        $this->dispatch('/search?q=ASDFASDF');
        $this->assertResponseStatusCode(200);
        $this->assertControllerName('whathood\controller\search');
    }
}
?>
