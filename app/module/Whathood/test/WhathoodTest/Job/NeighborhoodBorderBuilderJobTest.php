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

    public function testValidData() {
        $this->initDb();
        $serviceLocator = $this->getServiceLocator();

        $whathoodConfig = $serviceLocator->get('Whathood\Config');

        $factory = new \Whathood\Factory\NeighborhoodBorderBuilderJobFactory();

        $job = $factory->createService(\WhathoodTest\Bootstrap::getServiceManager());

        $job->setGridResolution(1);
        $neighborhood = $this->buildTestNeighborhood(); 
        $region = $neighborhood->getRegion(); 
        $this->m()->regionMapper()->save($region);

        $whathoodUser = new \Whathood\Entity\WhathoodUser(array(
            'ipAddress' => '0.0.0.0'
        ));

        $userPolygon = $this->buildUserPolygon(array(
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
            'neighborhood_id' => $neighborhood->getId()
        ));

        try {
            $job->execute();
        }
        catch(\Exception $e) {
            $this->fail("border job should not have thrown an exception: $e");
        }
    }
}
?>
