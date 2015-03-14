<?php

namespace Whathood\Mapper;

use Doctrine\ORM\Query\ResultSetMapping;
use 
class TestPointMapper extends BaseMapper {


    public function createByUserPolygons($user_polygons,$grid_resolution) {

        $ids = array();
        foreach( $user_polygons as $up) {
            array_push($ids,$up->getId());
        }

        $sql = "SELECT ST_AsText(unnest(whathood.makegrid_2d(ST_Collect(polygon),:grid_resolution))) as test_point FROM user_polygon up WHERE up.id IN (".join(',',$ids).")";

        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('test_point','test_point');
        $query = $this->em->createNativeQuery($sql,$rsm);
        $query->setParameter(':grid_resolution',$grid_resolution);
        $result = $query->getResult();
        return $this->_resultToTestPoints($result);
    }

    public static function _resultToTestPoints($result) {
        foreach($result as $row) {
            \Zend\Debug\Debug::dump($row);
            die("dying now");
        }
    }


}
