<?php

namespace WhathoodTest\Job;

use Whathood\Entity\Neighborhood;
use Whathood\Entity\UserPolygon;
use Whathood\Spatial\PHP\Types\Geometry\Polygon;
use Whathood\Spatial\PHP\Types\Geometry\LineString;
use Whathood\Spatial\PHP\Types\Geometry\Point;

/**
 * Description of NeighborhoodControllerTest
 *
 * @author Jim Smiley twitter:@jimRsmiley
 */
class NeighborhoodBorderBuilderJobTest extends \Whathood\PHPUnit\BaseTest {

    public function setUp() {
        parent::setUp();
    }

    public function testUserPolygons() {
        $this->initDb();
        $serviceLocator = $this->getServiceLocator();

        $whathoodConfig = $serviceLocator->get('Whathood\Config');

        $job = \Whathood\Job\NeighborhoodBorderBuilderJob::build(array(
            'gridResolution'        => 1,
            'heatmapGridResolution' => $whathoodConfig->heatmapGridResolution(),
            'mapperBuilder'         => $serviceLocator->get('Whathood\Mapper\Builder'),
            'logger'                => $serviceLocator->get('Whathood\Logger')
        ));

        $region = new \Whathood\Entity\Region(array(
            'name' => "Region_".$this->getTestName()
        ));
        $this->m()->regionMapper()->save($region);


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

        $ups = $this->m()->userPolygonMapper()->fetchAll();

        $neighborhood = $userPolygon->getNeighborhood();

        if (count($neighborhood->getUserPolygons()))
            die("should have had more than 0 user polygons");
        $job->setContent(array(
            'neighborhood' => $neighborhood,
        ));

        $job->execute();
    }
}
?>
