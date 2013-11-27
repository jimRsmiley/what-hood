<?php

use DoctrineExtensions\PHPUnit\OrmTestCase,
 Doctrine\ORM\Configuration,
 Doctrine\Common\Cache\ArrayCache,
 Doctrine\ORM\EntityManager,
 PHPUnit\Extensions\Database\Operation\Composite,
 PHPUnit\Extensions\Database\Operation\Factory,
 ApplicationTest\Bootstrap,
 Application\Entity\Neighborhood,
 Application\Entity\Region;
use Application\PHPUnit\DoctrineBaseTest;
use CrEOF\Spatial\PHP\Types\Geometry\Point;
/**
 * Description of NeighborhoodORMTest
 *
 * @author Jim Smiley twitter:@jimRsmiley
 */
class RegionMapperTest extends DoctrineBaseTest {

    protected $object = null;

    public function setUp() {
        parent::setUp();
        $this->initDb();
        $this->object = $this->regionMapper();
    }
    
    public function tearDown() {
        parent::tearDown();
    }
    
    public function testSave() {
        $region = new Region( array( 
            'name' => 'Reading',
            'center_point' => new Point( 0,1 ) 
        ) );
        $this->object->save($region);
        $this->assertNotNull($region->getId());
    }

    public function testFetchAll() {
        $region1 = new Region( array(
            'name' => 'Test 1',
            'center_point' => new Point( 0,1 ) 
            ) );
        $region2 = new Region( array(
            'name' => 'Test 2',
            'center_point' => new Point( 0,1 ) 
        ) );
        
        $this->regionMapper()->save($region1);
        $this->regionMapper()->save($region2);
        
        $regions = $this->regionMapper()->fetchAll();
        $this->assertTrue( count( $regions ) == 2 );
    }

    public function testFetchDistinctRegionNames() {
        $region1 = new Region( array('name' => 'Test 1','center_point' => new Point( 0,1 ) ) );
        $region2 = new Region( array('name' => 'Test 2','center_point' => new Point( 0,1 ) ) );
        
        $this->regionMapper()->save($region1);
        $this->regionMapper()->save($region2);
        
        $regions = $this->regionMapper()->fetchDistinctRegionNames();
        $this->assertTrue( count( $regions ) == 2 );
    }
 }

?>
