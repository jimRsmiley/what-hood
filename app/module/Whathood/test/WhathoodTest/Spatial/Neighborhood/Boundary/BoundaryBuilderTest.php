<?php

namespace WhathoodTest\Mapper;

use Whathood\Entity\Neighborhood;
use Whathood\Entity\UserPolygon;
use Whathood\Election\PointElection;
use Whathood\Election\PointElectionCollection;
use Whathood\Spatial\PHP\Types\Geometry\Point;

class BoundaryBuilderTest extends \Whathood\PHPUnit\BaseTest {

    public function testGenerateBorderPolygonTest() {

        $neighborhood1 = new Neighborhood(array(
            'id' => 1 ));
        $neighborhood2 = new Neighborhood(array(
            'id' => 2 ));

        $userPolygons = array(
            new UserPolygon(array(
                'neighborhood' => $neighborhood1
            )),
            new UserPolygon(array(
                'neighborhood' => $neighborhood1
            )),
            new UserPolygon(array(
                'neighborhood' => $neighborhood2
            ))
        );

        $logger = $this->getServiceLocator()->get('Whathood\Logger');
        $pointElections = array(
            PointElection::build(array(
                'user_polygons' => $userPolygons,
                'logger' => $logger,
                'point' => new Point(1,1),
            )),
            PointElection::build(array(
                'user_polygons' => $userPolygons,
                'logger' => $logger,
                'point' => new Point(1,2),
            )),
            PointElection::build(array(
                'user_polygons' => $userPolygons,
                'logger' => $logger,
                'point' => new Point(2,2),
            )),
            PointElection::build(array(
                'user_polygons' => $userPolygons,
                'logger' => $logger,
                'point' => new Point(3,3),
            )),
            PointElection::build(array(
                'user_polygons' => $userPolygons,
                'logger' => $logger,
                'point' => new Point(4,4),
            )),
            PointElection::build(array(
                'user_polygons' => $userPolygons,
                'logger' => $logger,
                'point' => new Point(5,5),
            )),
            PointElection::build(array(
                'user_polygons' => $userPolygons,
                'logger' => $logger,
                'point' => new Point(6,6),
            ))
        );

        $pointElectionCollection = new PointElectionCollection($pointElections);

        $builder = $this->getServiceLocator()
            ->get('Whathood\Spatial\Neighborhood\Boundary\BoundaryBuilder');

        $boundary = $builder->build($pointElectionCollection, $neighborhood1);

        $this->assertNotNull($boundary);

        $points = $pointElectionCollection->byNeighborhood($neighborhood1);

        $this->assertTrue( 7 == count($points) );
    }
}
