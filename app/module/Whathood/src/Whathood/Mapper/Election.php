<?php

namespace Whathood\Mapper;

use CrEOF\Spatial\PHP\Types\Geometry\Point;
use Whathood\Spatial\PHP\Types\Geometry\MultiPoint;
use Whathood\Election\PointElection;
use Whathood\Election\PointElectionCollection;

/**
 *  handles getting the calculations which determine what neighborhood a point
 *  exists in
 */
class Election extends BaseMapper {


    public function getCollection($user_polygons,$neighborhood_id,$grid_resolution) {
        $test_points = $this->m()->testPointMapper()->createByUserPolygons($user_polygons,$grid_resolution);
        return $this->buildPointElectionCollection($test_points);
    }

    /**
     * given an array of $user_polygons, returns a polygon representing the border
     *
     * @param array - user polygons
     * @param integer - neighborhood id
     * @param float - test point grid resolution
     * @param double - concave hull target precision
     * @return mixed - Polygon object
     */
    public function generateBorderPolygon($electionCollection,$neighborhood_id) {

        if (empty($electionCollection->getPoints()))
            throw new \InvalidArgumentException("electionCollection must have points");

        $neighborhood_points = $electionCollection->byNeighborhoodId($neighborhood_id);

        if (empty($neighborhood_points)) {
            $this->logger()->info("electionCollection did not return any neighborhood points dispite being given ".count($electionCollection->getPoints())." points");
            return null;
        }
        $points = array();
        foreach ($neighborhood_points as $p) {
            array_push($points,$p->getPoint());
        }

        $polygon = $this->m()->pointsAsPolygonMapper()
            ->toPolygon(new MultiPoint($points));
        return $polygon;
    }

    /**
     * given an array of test points, build a collection of election test points
     *
     * @param array - an array of test point objects
     * @return mixed - PointElectionCollection
     */
    public function buildPointElectionCollection($test_points) {
        $c_points = array();
        foreach ($test_points as $p) {
            array_push($c_points, $this->buildPointElection($p));
        }
        return new PointElectionCollection($c_points);
    }

    public function buildPointElection(Point $point) {
        $user_polygons = $this->m()->userPolygonMapper()->byPoint($point);
        $consensus_point = PointElection::build($point, $user_polygons);
        return $consensus_point;
    }
}

?>
