<?php

use 
 Whathood\Entity\Neighborhood,
 Whathood\Entity\Region,
 Whathood\Model\HeatMap\Point,
 CrEOF\Spatial\PHP\Types\Geometry\Polygon;

use Whathood\Entity\HeatMap;
use Whathood\PHPUnit\DoctrineBaseTest as BaseTest;

/**
 * Description of NeighborhoodORMTest
 *
 * @author Jim Smiley twitter:@jimRsmiley
 */
class HeatMapMapperTest extends BaseTest {

    protected $object;
    
    public function setUp() {
        parent::setUp();
        $this->object = $this->sm->get('Whathood\Mapper\HeatMapMapper');
    }

    public function testSave() {
        
        $heatMap = $this->getDummyHeatMap();
                
        $heatMapMapper = $this->sm->get('Whathood\Mapper\HeatMapMapper');
        
        $this->neighborhoodMapper()->save( $heatMap->getNeighborhood() );
        // no exception?  it probably worked out
        $heatMapMapper->save( $heatMap );
        $this->assertEquals( $heatMap->getId(), 1 );
    }
    
    /**
     * i want to be able to retrieve a list of neighborhoods from the db that
     * need to have their heatmaps updated, this should return both new neighborhoods
     * since they don't have heatmaps yet
     * 
     */
    public function testGetStaleHeatMapNeighborhoodsNoHeatmapsYet() {
        $neighborhood1 = $this->getDummyNeighborhood();
        $this->neighborhoodMapper()->save($neighborhood1);
        $n2 = $this->getDummyNeighborhood();
        $n2->setName("Test Neighborhood 2");
        $this->neighborhoodMapper()->save($n2);
        
        $neighborhoods = $this->object->getStaleNeighborhoods();
        
        $this->assertEquals( 2, count($neighborhoods) );
    }
    
    /**
     * @group 20130809
     */
    public function testGetStaleHeatMapNeighborhoodsWithHeatmapsAlready() {
        
        $tesNeighborhoodName = "TestNeighborhoodName123";

        $whathoodUser = $this->getDummyWhathoodUser();
        $whathoodUser->setUserName('user_testGetStaleHeatMapNeighborhoodsWithHeatmapsAlready');
        // remember to save the user first
        $this->whathoodUserMapper()->save( $whathoodUser );
        
        /*
         * create a neighborhoodPolygon and save it
         */
        $np1 = $this->getDummyNeighborhoodPolygon($whathoodUser);
        $np1->getNeighborhood()->setName($tesNeighborhoodName);
        $this->neighborhoodPolygonMapper()->save($np1);

        /*
         * generate and save a heat map
         */
        $heatMap = $this->getDummyHeatMap();
        $heatMap->setNeighborhood( $np1->getNeighborhood() );
        $this->object->save( $heatMap );
        
        /*
         * save a second, neighborhood
         */
        $np2 = $this->getDummyNeighborhoodPolygon( $this->getDummyWhathoodUser() );
        $np2->setWhathoodUser( $np1->getWhathoodUser() );
        $np2->getNeighborhood()->setName('$tesNeighborhoodName2 222');
        $this->neighborhoodPolygonMapper()->save($np2);
        
        /*
         * save a second test neighborhood that should force the heat map mapper
         * to realize it's newer than the current heatmap
         */
        \Zend\Debug\Debug::dump( $whathoodUser );
        $np1 = $this->getDummyNeighborhoodPolygon($whathoodUser);
        $np1->getNeighborhood()->setName($tesNeighborhoodName);
        $this->neighborhoodPolygonMapper()->save($np1);
        
        $staleNeighborhoods = $this->object->getStaleNeighborhoods();
        
        $this->assertEquals( 1, count( $staleNeighborhoods ) );
        $this->assertEquals( $tesNeighborhoodName, $staleNeighborhoods[0]->getName() );
    }
 }

?>
