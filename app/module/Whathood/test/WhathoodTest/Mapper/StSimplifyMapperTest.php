<?php

namespace WhathoodTest\Mapper;

use Whathood\Spatial\PHP\Types\Polygon;

class StSimplifyMapperTest extends \Whathood\PHPUnit\BaseTest {

    public function testSimplify() {
        $this->initDb();
        $polygon = $this->buildTestPolygon();

        $object = $this->getServiceLocator()
            ->get('Whathood\Mapper\StSimplifyMapper');

        $new_polygon = $object->simplify($polygon, 1);

        $this->assertNotNull($new_polygon);
        $this->assertInstanceOf('Whathood\Spatial\PHP\Types\Geometry\Polygon', $new_polygon);
    }
}
