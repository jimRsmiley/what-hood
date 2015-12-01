<?php

namespace Whathood\Spatial\Neighborhood\Boundary;

use Whathood\Election\PointElectionCollection;
use Whathood\Entity\Neighborhood;
use Whathood\Spatial\PHP\Types\Geometry\MultiPoint;

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

        $neighborhood_points = $pointElectionCollection->byNeighborhood($neighborhood);

        if (empty($neighborhood_points)) {
            return null;
        }
        $points = array();
        foreach ($neighborhood_points as $p) {
            array_push($points,$p->getPoint());
        }

        $polygon = $this->getMapper('pointsAsPolygon')
            ->toPolygon(new MultiPoint($points));
        return $polygon;
    }
}
