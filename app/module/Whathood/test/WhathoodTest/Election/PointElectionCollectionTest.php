<?php

namespace WhathoodTest\Election;

use Whathood\Election\PointElectionCollection;
use Whathood\Election\PointElection;

class PointElectionCollectionTest extends \Whathood\PHPUnit\BaseTest {

    public function testByNeighborhood() {
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
        );
        $peCollection = new PointElectionCollection($objects);

        $this->assertEquals(count($peCollection->byNeighborhood($neighborhood1)), 5);
    }
}
