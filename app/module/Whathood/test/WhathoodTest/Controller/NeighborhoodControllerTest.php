<?php

namespace WhathoodTest\Controller;

use Zend\Http\Request;
use Zend\Mvc\Router\RouteMatch;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\Router\Http\TreeRouteStack as HttpRouter;
use Zend\Stdlib\Parameters;
use Whathood\PHPUnit\BaseControllerTest;
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
class NeighborhoodControllerTest extends BaseControllerTest {

    public function setUp() {
        parent::setUp();
    }

    /**
     *  issue: when the neighborhood /Region/Neighborhood is pulled for neighborhoods that
     *  have user polygons drawn but no neighborhood borders drawn, they fail
     */
    public function testNeighborhoodsWithNoBorders() {
        $this->initDb();
        $region = new \Whathood\Entity\Region(array(
            'name' => 'TestRegion'.rand(0,99999999)
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
            )
        );

        $this->m()->userPolygonMapper()->save($userPolygon);

        $this->dispatch(sprintf("/%s/%s",
            $region->getName(), $neighborhood->getName()));

        $this->assertResponseStatusCode(200);
        $this->assertControllerName('whathood\controller\neighborhood');
    }
}
?>
