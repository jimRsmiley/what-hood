<?php

namespace Whathood\Spatial\Neighborhood\Boundary;

use Whathood\Election\PointElectionCollection;
use Whathood\Entity\Neighborhood;
use Whathood\Spatial\PHP\Types\Geometry\MultiPoint;

/**
 * handle building of a boundary
 *
 * This means taking a PointElectionCollection and a neighborhood and returning a boundary for this neighborhood
 **/
class BoundaryBuilder {

    use \Whathood\Mapper\MapperTrait;

    /**
     * given a collection of ElectionPoints, builds the neighborhood border for the given neighborhood id
     *
     * @param electionCollect \Whathood\Election\PointElectionCollection - a collection of ElectionPoints
     * @param neighbood_id int - the neighborhood id to generate the border for
     *
     * @return mixed - Polygon object
     */
    public function build(PointElectionCollection $pointElectionCollection, Neighborhood $neighborhood) {

        if (empty($pointElectionCollection))
            throw new \InvalidArgumentException("pointElectionCollection must be defined");
        if (empty($pointElectionCollection->getPointElections()))
            throw new \InvalidArgumentException("electionCollection must have points");

        // these are the neighborhood points we'll use to wrap the border
        $neighborhood_points = $pointElectionCollection->byNeighborhood($neighborhood);

        if (empty($neighborhood_points))
            throw new \Exception("neighborhood did not win any points in election");

        $points = $this->getStandardPoints($neighborhood_points);

        return $this->getMapper('pointsAsPolygon')
            ->toPolygon(new MultiPoint($points));
    }

    /**
     * we get PointElections back from asking the pointElectionCollection for points by neighborhood,
     * so lets convert those points to regular points before we ask the pointsAsPolygon mapper to wrap them
     *
     * @param  array \Whathood\Election\PointElection
     * @return array Point
     */
    public function getStandardPoints($electionPoints) {
        $points = array();
        foreach ($electionPoints as $p) {
            array_push($points,$p->getPoint());
        }
        return $points;
    }

}
