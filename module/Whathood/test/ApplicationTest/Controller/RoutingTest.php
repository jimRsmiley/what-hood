<?php

namespace ApplicationTest\Controller;

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
class RoutingTest extends \Application\PHPUnit\BaseControllerTest {

    public function setUp()
    {
        $this->setApplicationConfig(
            //include '../../../../config/application.config.php'
            include 'TestConfig.php'
        );
        parent::setUp();
        $this->initDb();
    }
    
    public function testRedirect() {
        $this->getRequest()->setMethod('GET');
        
        $this->dispatch( '/n/add' );
        
        $this->assertResponseStatusCode(302);
    }
    
    /**
     * @depends testRedirect
     */
    public function testValidAuth() {
        
        $this->getRequest()->setMethod('GET');
        
        $this->dispatch( '/n/add' );
        
        $this->assertResponseStatusCode(302);
        
        $whathoodUser = $this->getSavedAuthenticatedWhathoodUser();
        
        $this->dispatch( '/n/add?region_name=Philadelphia' );
        
        $this->printResponse();
        exit;
        $this->assertResponseStatusCode(302);
    }

    /*
     * @depends testRedirect
     */
    public function testInvalidAuth() {
        
        $this->getRequest()->setMethod('GET');
        
        $this->dispatch( '/n/add' );
        
        $this->assertResponseStatusCode(302);
        
        $auth = Bootstrap::getServiceManager()->get('Application\Model\AuthenticationService');
        
        $auth->setWhathoodUser( new \Application\Entity\WhathoodUser( array(
            'id' => 1,
        )));

        try {
            $this->dispatch( '/n/add' );
             //die( $this->getResponse()->getBody() );
            $this->fail();
        } catch( AuthenticationException $e ) {
            
            
        } 
        
        // excect this
        catch( \FacebookApiException $e ) {
            
        }
        $this->assertTrue(true);
    }

    public function testHome() {
        
        $this->getRequest()
                ->setMethod('GET');
        
        $this->dispatch( '/');
        
        $this->assertResponseStatusCode(200);
        $this->assertControllerName('application\controller\index');
    }
    
    
}
?>