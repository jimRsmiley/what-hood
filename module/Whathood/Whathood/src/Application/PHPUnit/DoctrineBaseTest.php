<?php
namespace Application\PHPUnit;

use ApplicationTest\Bootstrap;
use Application\Spatial\PHP\Types\Geometry\LineString;
use Application\Spatial\PHP\Types\Geometry\Point;
use Application\Spatial\PHP\Types\Geometry\Polygon;
use Application\Entity\Neighborhood;
use Application\Entity\NeighborhoodVote;
use Application\Entity\Region;
use Application\Entity\HeatMap;
use Application\Entity\WhathoodUser;
use Application\Entity\FacebookUser;
use Application\Entity\NeighborhoodPolygon;
use Application\Model\HeatMap\Point as HeatMapPoint;
/**
 * responsible for anything to do with the database
 *
 * @author Jim Smiley twitter:@jimRsmiley
 */
class DoctrineBaseTest extends \PHPUnit_Framework_TestCase {
    
    public function setUp() {
        if( getenv('APPLICATION_ENV') !==  'test' )
            throw new \Exception("you must set APPLICATION_ENV to 'test'");
        
        parent::setUp();
    }
    
    public function tearDown() {}
    
    
    public function initDb() {
        
        $DEBUG = false;
        // Retrieve the Doctrine 2 entity manager
        $em = $this->getServiceManager()->get('mydoctrineentitymanager');

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
        
        // don't really know why we have to clear this but it works to stop
        // it when the entity manager doesn't seem to persist shit
        $em->clear();
    }
    
    private $whathoodUserMapper   = null;
    private $neighborhoodMapper   = null;
    private $neighborhoodPolygonMapper = null;
    private $neighborhoodVoteMapper   = null;
    private $regionMapper         = null;

    public function whathoodUserMapper() {
        if( $this->whathoodUserMapper == null )
            $this->whathoodUserMapper = $this->getServiceManager()->get('Application\Mapper\WhathoodUserMapper');
            
        return $this->whathoodUserMapper;
    }
    
    public function neighborhoodMapper() {
        if( $this->neighborhoodMapper == null )
            $this->neighborhoodMapper = $this->getServiceManager()->get('Application\Mapper\NeighborhoodMapper');
        
        return $this->neighborhoodMapper;
    }
    
    public function neighborhoodPolygonMapper() {
        if( $this->neighborhoodPolygonMapper == null )
            $this->neighborhoodPolygonMapper = $this->getServiceManager()->get('Application\Mapper\NeighborhoodPolygonMapper');
        
        return $this->neighborhoodPolygonMapper;
    }
    
    public function neighborhoodVoteMapper() {
        if( $this->neighborhoodVoteMapper == null )
            $this->neighborhoodVoteMapper = 
                $this->getServiceManager()->get('Application\Mapper\NeighborhoodPolygonVoteMapper');
        
        return $this->neighborhoodVoteMapper;
    }
    
    public function regionMapper() {
        if( $this->regionMapper == null )
            $this->regionMapper = $this->getServiceManager()->get('Application\Mapper\RegionMapper');
        
        return $this->regionMapper;
    }
  
    
    public function getSavedUser($userName = null) {
        
        if( empty($userName) )
            $userName = 'saved user';
        
        $whathoodUser = DummyEntityBuilder::whathoodUser();
        $whathoodUser->setUserName($userName);
        $this->whathoodUserMapper()->save( $whathoodUser );
        return $whathoodUser;
    }
    
    public function getSavedNeighborhoodPolygon( $userName, $PolygonName ) {
        $whathoodUser = $this->getSavedUser($userName);
        $neighborhoodPolygon = DummyEntityBuilder
                                        ::neighborhoodPolygon($whathoodUser);
        $this->neighborhoodPolygonMapper()->save( $neighborhoodPolygon );

        return $neighborhoodPolygon;
    }
    
    public function getServiceManager() {
        return Bootstrap::getServiceManager();
    }
}

?>
