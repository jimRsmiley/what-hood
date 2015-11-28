<?php

namespace WhathoodTest\Job;

use Whathood\Entity\Neighborhood;
use Whathood\Entity\UserPolygon;
use Whathood\Spatial\PHP\Types\Geometry\Polygon;
use Whathood\Spatial\PHP\Types\Geometry\LineString;
use Whathood\Spatial\PHP\Types\Geometry\Point;

/**
 * Description of NeighborhoodControllerTest
 */
class HeatmapBuilderJobTest extends \Whathood\PHPUnit\BaseTest {

    public function setUp() {
        parent::setUp();
    }

    public function testValidData() {
        $this->initDb();

        /**
         * set up test data
         **/

        $region = new \Whathood\Entity\Region(array(
            'name' => "Region_".$this->getTestName()
        ));

        $whathoodUser = new \Whathood\Entity\WhathoodUser(array(
            'ipAddress' => '0.0.0.0'
        ));

        $neighborhood = new Neighborhood(array(
            'id'   => 1,
            'name' => "MyTest" . $this->getTestName(),
            'region' => $region,
        ));

        $userPolygon =
            new UserPolygon(array(
                'polygon' => Polygon::build(array(
                    new LineString( array(
                            new Point(30,40),
                            new Point(30,50),
                            new Point(40,50),
                            new Point(30,40)
                            )
                        ),
                    ),
                    4326
                ),
                'neighborhood' => $neighborhood,
                'region' => $region,
                'whathoodUser' => $whathoodUser
            ));
        $this->m()->userPolygonMapper()->save($userPolygon);

        /*
         * now run the job
         *
         **/
        $factory = new \Whathood\Factory\HeatmapBuilderJobFactory();
        $job = $factory->createService(\WhathoodTest\Bootstrap::getServiceManager());
        $job->setGridResolution(1);

        if (count($neighborhood->getUserPolygons()))
            die("should have had more than 0 user polygons");
        $job->setContent(array(
            'neighborhood_id' => $userPolygon->getNeighborhood()->getId()
        ));
        $job->execute();
    }
}
?>
