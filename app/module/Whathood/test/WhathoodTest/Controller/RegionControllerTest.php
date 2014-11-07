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
class RegionControllerTest extends \Whathood\PHPUnit\BaseControllerTest {

    public function setUp()
    {
        $this->setApplicationConfig(
            //include '../../../../config/application.config.php'
            include 'TestConfig.php'
        );
        parent::setUp();
    }
    
    public function testListRegions() {
        
        $this->getRequest()
                ->setMethod('GET');
        
        $this->dispatch( '/whathood/region/list-regions');
        $this->printResponse();
        $this->assertResponseStatusCode(200);
    }
   
}
?>