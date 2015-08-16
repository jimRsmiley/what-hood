<?php

namespace Whathood\Election;

use Whathood\Entity\Neighborhood;
use Whathood\Entity\HeatMapPoint;
/**
 * store whathood PointElections and provide easy retreival methods
 *
 */
class PointElectionCollection {

    protected $_points;

    public function __construct($points) {
        $this->_points = $points;
    }

    public function getPoints() {
        return $this->_points;
    }

    /**
     * return the points that belong to the neighborhood
     */
    public function byNeighborhoodId($n_id) {
        $points = array();
        foreach($this->_points as $p) {
            if (!$p->isTie())
                if($p->isWinner($n_id))
                    array_push($points,$p);
        }
        return $points;
    }

    public function heatMapPointsByNeighborhood(Neighborhood $neighborhood) {
        $heatmap_points = array();
        foreach ($this->_points as $ep) {
            $cn = $ep->candidateNeighborhood($neighborhood);
            $percentage = $cn->getNumVotes() / $ep->totalVotes();
            array_push($heatmap_points, HeatMapPoint::build( array(
                'neighborhood' => $neighborhood,
                'point' => $ep->getPoint(),
                'percentage' => $percentage )));
        }
        return $heatmap_points;
    }
}
