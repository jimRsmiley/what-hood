<?php

namespace WhathoodTest\Mapper;

use Whathood\Entity\Neighborhood;
use Whathood\Entity\UserPolygon;
use Whathood\Election\PointElection;
use Whathood\Election\PointElectionCollection;
use Whathood\Spatial\PHP\Types\Geometry\Point;

class BoundaryBuilderTest extends \Whathood\PHPUnit\BaseTest {

    public function testGenerateBorderPolygonTest() {

        $neighborhood1 = $this->buildTestNeighborhood();
        $neighborhood2 = $this->buildTestNeighborhood();

        $userPolygons = array(
            static::buildTestUserPolygon(array(
                'neighborhood' => $neighborhood1
            )),
            static::buildTestUserPolygon(array(
                'neighborhood' => $neighborhood1
            )),
            static::buildTestUserPolygon(array(
                'neighborhood' => $neighborhood2
            ))
        );

        // create 5 PointElections
        $objects = array(
            $this->buildTestPointElection(array(
                'user_polygons' => $userPolygons
            )),
            $this->buildTestPointElection(array(
                'user_polygons' => $userPolygons
            )),
            $this->buildTestPointElection(array(
                'user_polygons' => $userPolygons
            )),
            $this->buildTestPointElection(array(
                'user_polygons' => $userPolygons
            )),
            $this->buildTestPointElection(array(
                'user_polygons' => $userPolygons
            )),
            $this->buildTestPointElection(array(
                'user_polygons' => $userPolygons
            )),
            $this->buildTestPointElection(array(
                'user_polygons' => $userPolygons
            )),
        );
        $peCollection = new PointElectionCollection($objects);

        $builder = $this->getServiceLocator()
            ->get('Whathood\Spatial\Neighborhood\Boundary\BoundaryBuilder');

        $boundary = $builder->build($peCollection, $neighborhood1);

        $this->assertNotNull($boundary);

        $points = $peCollection->byNeighborhood($neighborhood1);

        $this->assertTrue( 7 == count($points) );
    }
}
