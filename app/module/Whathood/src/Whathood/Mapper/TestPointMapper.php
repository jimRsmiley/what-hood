<?php

namespace Whathood\Mapper;

use Doctrine\ORM\Query\ResultSetMapping;
use Whathood\Spatial\PHP\Types\Geometry\Point;

class TestPointMapper extends BaseMapper {


    /**
     * return an array of points in a grid with resolution that covers all user_polygons
     *
     * @param array user_polygons an array of UserPolygons
     * @param string grid_resolution
     *
     */
    public function createByUserPolygons($user_polygons,$grid_resolution) {

        if (empty($grid_resolution))
            throw new \InvalidArgumentException('grid_resolution may not be empty');
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

        if (empty($result))
            throw new \Whathood\Exception("no points were returned in makegrid");

        if (empty($result))
            return null;
        return $this->_doctrineResultToPoints($result,'test_point');
    }

    /**
     *
     * take an array of doctrine result rows and turn them into points
     *
     * @param $result - a doctrine result array
     * @param $key_str - the column header string for the point text
     *
     */
    public static function _doctrineResultToPoints($result,$key_str) {
        $points = array();
        foreach($result as $row) {
            $p = Point::buildFromText($row[$key_str]);
            array_push($points, $p);
        }
        return $points;
    }
}
