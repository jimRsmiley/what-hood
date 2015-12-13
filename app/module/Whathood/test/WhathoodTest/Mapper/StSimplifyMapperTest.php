<?php

namespace WhathoodTest\Mapper;

class StSimplifyMapperTest extends \Whathood\PHPUnit\BaseTest {

    public function testSimplify() {
        $this->initDb();
        $polygon = $this->buildTestPolygon();

        $object = $this->getServiceLocator()
            ->get('Whathood\Mapper\StSimplifyMapper');

        $new_polygon = $object->simplify($polygon, 1);

        $this->assertNotNull($new_polygon);
    }
}
