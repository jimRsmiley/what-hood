<?php

namespace Whathood\Mapper;

use CrEOF\Spatial\PHP\Types\Geometry\Point;
use Whathood\Spatial\PHP\Types\Geometry\MultiPoint;
use Whathood\ElectionPoint;
use Whathood\ElectionPointCollection;

/**
 *  handles getting the calculations which determine what neighborhood a point
 *  exists in
 */
class Election extends BaseMapper {

    /**
     * given an array of $user_polygons, returns a polygon representing the border
     *
     * @param array - user polygons
     * @param integer - neighborhood id
     * @param float - test point grid resolution
     * @param double - concave hull target precision
     * @return mixed - Polygon object
     */
    public function generateBorderPolygon($user_polygons,$neighborhood_id,$grid_resolution,$target_percentage) {
        $test_points = $this->m()->testPointMapper()->createByUserPolygons($user_polygons,$grid_resolution);
        $electionCollection = $this->buildElectionPointCollection($test_points);

        $neighborhood_points = $electionCollection->byNeighborhoodId($neighborhood_id);

        $points = array();
        foreach ($neighborhood_points as $p) {
            array_push($points,$p->getPoint());
        }

        $polygon = $this->m()->concaveHullMapper()
            ->toPolygon(new MultiPoint($points),$target_percentage);

        return $polygon;
    }

    /**
     * given an array of test points, build a collection of election test points
     *
     * @param array - an array of test point objects
     * @return mixed - ElectionPointCollection
     */
    public function buildElectionPointCollection($test_points) {
        $c_points = array();
        foreach ($test_points as $p) {
            array_push($c_points, $this->buildElectionPoint($p));
        }
        return new ElectionPointCollection($c_points);
    }

    public function buildElectionPoint(Point $point) {
        $user_polygons = $this->m()->userPolygonMapper()->byPoint($point);
        $consensus_point = ElectionPoint::build($point, $user_polygons);
        return $consensus_point;
    }
}

?>
