<?php

namespace WhathoodTest\Controller\Restful;

use Zend\Http\Request;
use Zend\Mvc\Router\RouteMatch;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\Router\Http\TreeRouteStack as HttpRouter;
use Zend\Stdlib\Parameters;
use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase,
 ApplicationTest\Bootstrap;
use Whathood\PHPUnit\BaseControllerTest;
use Whathood\Entity\Neighborhood;

class HeatmapRestfulControllerTest extends BaseControllerTest {

    public function setUp() {
        parent::setUp();
    }

    public function testHome() {
        $this->initDb();

        $neighborhood = new Neighborhood(array(
            'name' => "MY TEST NEIGHBORHOOD",
            'region' => new \Whathood\Entity\Region(array(
                'name' => 'TestRegion'.rand(0,9999999)
            ))
        ));
        $this->m()->neighborhoodMapper()->save($neighborhood);

        $neighborhood_id = $neighborhood->getId();

        $this->getRequest()
                ->setMethod('GET');
        $this->dispatch( "/api/v1/heatmap-points/neighborhood_id/$neighborhood_id");

        $this->assertResponseStatusCode(200);
        $this->assertControllerName('whathood\controller\restful\heatmappoint');
    }
}
?>
