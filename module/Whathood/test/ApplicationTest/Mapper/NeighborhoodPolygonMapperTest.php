<?php

use 
 Whathood\Entity\Neighborhood,
 Whathood\Entity\Region,
 Whathood\Spatial\PHP\Types\Geometry\Point,
 Whathood\Spatial\PHP\Types\Geometry\LineString,
 Whathood\Spatial\PHP\Types\Geometry\Polygon;

use Whathood\Entity\WhathoodUser;
use Whathood\PHPUnit\DoctrineBaseTest;
use Whathood\PHPUnit\DummyEntityBuilder;

/**
 * Description of NeighborhoodORMTest
 *
 * @author Jim Smiley twitter:@jimRsmiley
 */
class NeighborhoodPolygonMapperTest extends DoctrineBaseTest {

    protected $object;
    
    public function setUp() {
        parent::setUp();
        $this->initDb();
        $this->object = $this->neighborhoodPolygonMapper();
        
    }

    public function testGetLatest() {
        $whathoodUser = $this->getSavedUser();
        $np = DummyEntityBuilder::neighborhoodPolygon($whathoodUser);
        $this->object->save($np);
        $this->assertEquals( 1, $np->getId() );
        
        $latest = $this->object->getLatest($np->getNeighborhood());
        $this->assertEquals( 1, $latest->getId() );
    }
    

    public function testSave() {
        $whathoodUser = $this->getSavedUser();
        $neighborhoodPolygon = DummyEntityBuilder::neighborhoodPolygon($whathoodUser);
                
        $this->object->save( $neighborhoodPolygon );
        
        // no exception?  it probably worked out
        $this->assertTrue(true);
    }
    
    /*
     * @group 20131104
     */
    public function testByIdDeletedPolygon() {
        
        $deletedStatus = true;
        $whathoodUser = $this->getSavedUser();
        $neighborhoodPolygon = DummyEntityBuilder::neighborhoodPolygon($whathoodUser);
        $neighborhoodPolygon->setDeleted( $deletedStatus );
        $this->object->save( $neighborhoodPolygon );
        
        $neighborhoodPolygon2 = $this->object->byId('1');
        $this->assertEquals( '1', $neighborhoodPolygon2->getId() );
        $this->assertEquals( $deletedStatus,$neighborhoodPolygon2->getDeleted() );
    }
    
    /*
     * @depends testByIdDeletedPolygon
     */
    public function testSetNeighborhoodPolygonAsDeleted() {
        
        $whathoodUser = $this->getSavedUser();
        $neighborhoodPolygon = DummyEntityBuilder::neighborhoodPolygon($whathoodUser);
        
        $this->object->save( $neighborhoodPolygon );
        
        $neighborhoodPolygon = $this->object->byId('1');
        $this->assertEquals( '1', $neighborhoodPolygon->getId() );
        $this->assertFalse( $neighborhoodPolygon->getDeleted() );
        
        $neighborhoodPolygon->setDeleted(true);
        
        $this->object->update( $neighborhoodPolygon );
        
        try {
            $np2 = $this->object->byId( '1' );
        }
        catch( \Doctrine\ORM\NoResultException $e ) {
            $this->assertTrue(false,"no result exception caught");
        }
        $this->assertTrue( $np2->getDeleted() );
        
    }
    
    /**
     * @group testByPoint
     */
    public function testByPoint() {
        $whathoodUser = $this->getSavedUser();
        // create a neighborhood and save it
        $neighborhoodPolygon = DummyEntityBuilder::neighborhoodPolygon($whathoodUser);
        $polygon = new Polygon( 
                array( 
                    array( 
                        new Point(0,0),
                        new Point(10,0),
                        new Point(10,10),
                        new Point(0,10),
                        new Point(0,0)
        )));
        $neighborhoodPolygon->setPolygon($polygon);
        
        $testPoint = new Point( 5, 5 );
        
        $this->object->save( $neighborhoodPolygon );
        
        $neighborhoods = $this->object->byPoint( $testPoint );
        
        $this->assertTrue( count( $neighborhoods ) === 1 );
    }
 }

?>
