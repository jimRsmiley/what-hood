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
class PointElectionMapper extends BaseMapper {


    public function getCollection($user_polygons,$neighborhood_id,$grid_resolution) {
        $test_points = $this->m()->testPointMapper()->createByUserPolygons($user_polygons,$grid_resolution);

        if (empty($test_points))
            throw new \Whathood\Exception("no test points were created");
        return $this->buildPointElectionCollection($test_points);
    }

    /**
     * given an array of $user_polygons, returns a polygon representing the border
     *
     * @param electionCollect \Whathood\Collection - a colelction of ElectionPoints
     * @param neighbood_id int - the neighborhood id to generate the border for
     *
     * @return mixed - Polygon object
     */
    public function generateBorderPolygon($electionCollection, $neighborhood_id) {

        if (empty($electionCollection->getPointElections()))
            throw new \InvalidArgumentException("electionCollection must have points");

        $neighborhood_points = $electionCollection->byNeighborhoodId($neighborhood_id);

        if (empty($neighborhood_points)) {
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
    public function buildPointElectionCollection(array $test_points) {
        if (empty($test_points))
            throw new \InvalidArgumentException("test_points may not be empty");
        $c_points = array();
        foreach ($test_points as $p) {
            $user_polygons = $this->m()->userPolygonMapper()->byPoint($p);
            array_push($c_points, PointElection::build(array(
                'point' => $p,
                'userPolygons' => $user_polygons,
                'logger' => $this->logger()
            )));
        }
        return new PointElectionCollection($c_points);
    }
}
?>
