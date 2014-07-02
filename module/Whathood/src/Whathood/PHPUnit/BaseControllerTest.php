<?php
namespace Whathood\PHPUnit;

use ApplicationTest\Bootstrap;
use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;
/**
 * Description of NeighborhoodControllerTest
 *
 * @author Jim Smiley twitter:@jimRsmiley
 */
class BaseControllerTest extends AbstractHttpControllerTestCase {

    protected $sm;
    
    public function setUp()
    {
        parent::setUp();
        
        if( getenv('APPLICATION_ENV') !==  'test' )
            throw new \Exception("you must set APPLICATION_ENV to 'test'");
        
        $this->sm = Bootstrap::getServiceManager();
        
        $this->setApplicationConfig(
            include 'TestConfig.php'
        );
    }
    
    public function printResponse() {
        print $this->getResponse()->getBody();
        
    }
    
    public function initDb() {
        
        $DEBUG = false;
        // Retrieve the Doctrine 2 entity manager
        $em = $this->sm->get('doctrine.entitymanager.orm_default');

        // Instantiate the schema tool
        $tool = new \Doctrine\ORM\Tools\SchemaTool($em);

        // Retrieve all of the mapping metadata
        $classes = $em->getMetadataFactory()->getAllMetadata();
        
        if( $DEBUG ) print "dropping schema\n";
        // Delete the existing test database schema
        $tool->dropSchema($classes);
        
        if( $DEBUG ) print "creating schema\n";
        // Create the test database schema
        $tool->createSchema($classes);
        if( $DEBUG ) print "schema created\n";
    }

    private $whathoodUserMapper   = null;
    private $neighborhoodMapper   = null;
    private $neighborhoodPolygonMapper = null;
    private $neighborhoodVoteMapper   = null;
    private $regionMapper         = null;

    public function whathoodUserMapper() {
        return $this->whathoodUserMapper = $this->sm->get(
                                                    'Whathood\Mapper\WhathoodUserMapper');
    }
    
    public function neighborhoodMapper() {
        if( $this->neighborhoodMapper == null )
            $this->neighborhoodMapper = $this->sm->get('Whathood\Mapper\NeighborhoodMapper');
        
        return $this->neighborhoodMapper;
    }
    
    public function userPolygonMapper() {
        if( $this->neighborhoodPolygonMapper == null )
            $this->neighborhoodPolygonMapper = $this->sm->get('Whathood\Mapper\UserPolygonMapper');
        
        return $this->neighborhoodPolygonMapper;
    }
    
    public function neighborhoodVoteMapper() {
        if( $this->neighborhoodVoteMapper == null )
            $this->neighborhoodVoteMapper = 
                $this->sm->get('Whathood\Mapper\NeighborhoodPolygonVoteMapper');
        
        return $this->neighborhoodVoteMapper;
    }
    
    public function regionMapper() {
        if( $this->regionMapper == null )
            $this->regionMapper = $this->sm->get('Whathood\Mapper\Region');
        
        return $this->regionMapper;
    }
    
    public function getAuthenticationService() {
        return $this->sm->get('Whathood\Model\AuthenticationService');
    }
    
    public function getSavedAuthenticatedWhathoodUser($userName = null) {
        
        if( empty($userName) )
            $userName = 'saved and authenticated userName';
        
        $whathoodUser = DummyEntityBuilder::whathoodUser();
        $whathoodUser->setUserName($userName);
        $this->whathoodUserMapper()->save( $whathoodUser );
        $this->getAuthenticationService()->setWhathoodUser( $whathoodUser );
        return $whathoodUser;
    }
    
    public function getSavedUser($userName = null) {
        
        if( empty($userName) )
            $userName = 'saved user';
        
        $whathoodUser = DummyEntityBuilder::whathoodUser();
        $whathoodUser->setUserName($userName);
        $this->whathoodUserMapper()->save( $whathoodUser );
        $this->getAuthenticationService()->setWhathoodUser( $whathoodUser );
        return $whathoodUser;
    }
    
    public function getSavedNeighborhoodPolygon( $userName, $PolygonName ) {
        $whathoodUser = $this->getSavedUser($userName);
        $neighborhoodPolygon = DummyEntityBuilder::neighborhoodPolygon($whathoodUser);
        $this->neighborhoodMapper->save( $neighborhoodPolygon );
        return $neighborhoodPolygon;
    }
}
?>