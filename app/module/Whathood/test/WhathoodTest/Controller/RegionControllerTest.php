<?php

namespace WhathoodTest\Controller;

use Zend\Http\Request;
use Zend\Mvc\Router\RouteMatch;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\Router\Http\TreeRouteStack as HttpRouter;
use Zend\Stdlib\Parameters;
use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase,
 ApplicationTest\Bootstrap;
use Whathood\Entity\Region;
/**
 * Description of NeighborhoodControllerTest
 *
 * @author Jim Smiley twitter:@jimRsmiley
 */
class RegionControllerTest extends \Whathood\PHPUnit\BaseControllerTest {

    public function setUp()
    {
        $this->setApplicationConfig(
            //include '../../../../config/application.config.php'
            include 'TestConfig.php'
        );
        parent::setUp();
    }

    public function testRegionByNameUrl() {
        $region = new Region(array(
            'name' => "Test_Region_".$this->random_number()
        ));
        $this->m()->regionMapper()->save($region);

        $this->getRequest()
                ->setMethod('GET');
        $this->dispatch( '/Test%20Region');
        $this->assertResponseStatusCode(200);
    }

    public function testRegionUrl() {
        $this->getRequest()
                ->setMethod('GET');
        $this->dispatch( '/');
        $this->assertResponseStatusCode(200);
    }

    public function testListRegionsUrl() {
        $this->getRequest()
                ->setMethod('GET');
        $this->dispatch( '/whathood/region/list-regions');
        $this->assertResponseStatusCode(200);
    }

}
?>
