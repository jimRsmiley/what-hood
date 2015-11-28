<?php

namespace WhathoodTest\Mapper;

class PointElectionMapperTest extends \Whathood\PHPUnit\BaseTest {

    public function testGenerateBorderPolygonTest() {

        $up1 = UserBorder::build();
        $up2 = ;
        $up3 = ;
        $neighborood = new Neighborhood();

        $election_collection = new Collection();

        $mapper = \Whathood\Mapper\PointElectionMapper();

        $border = $mapper->generateBorderPolygon(
            $election_collection, $neighborhood->getId() );

        \Zend\Debug\Debug::dump($border);
    }
}
