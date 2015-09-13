<?php

namespace WhathoodTest\Mapper;

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
class UserPolygonTest extends \Whathood\PHPUnit\BaseTest {

    public function setUp() {
        parent::setUp();
    }

    public function testSaveUserPolygon() {
        $this->initDb();
        $serviceLocator = $this->getServiceLocator();

        $whathoodConfig = $serviceLocator->get('Whathood\Config');

        $factory = new \Whathood\Factory\NeighborhoodBorderBuilderJobFactory();

        $job = $factory->createService(\WhathoodTest\Bootstrap::getServiceManager());


        $region = new \Whathood\Entity\Region(array(
            'name' => 'Test Region'
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

        $this->assertFalse( empty($userPolygon->GetId()) );
        $this->assertFalse( empty($userPolygon->getNeighborhood()) );
        $this->assertFalse( empty($userPolygon->getNeighborhood()->getId()) );

        print "====>".$userPolygon->getNeighborhood()->getId();
    }
}
?>
