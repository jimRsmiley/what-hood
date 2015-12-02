<?php

namespace Whathood\Election;

use Whathood\Entity\Neighborhood;
use Whathood\Entity\HeatMapPoint;
/**
 * store whathood PointElections and provide easy retreival methods
 *
 */
class PointElectionCollection {

    protected $_point_elections;

    public function __construct(array $pointElections = null) {

        if (empty($pointElections))
            throw new \InvalidArgumentException("pointElections must be an array containing PointElections");
        $this->_point_elections = $pointElections;
    }

    public function getPointElections() {
        return $this->_point_elections;
    }

    /**
     * get the peak count of user neighborhoods for the given neighborhood in
     * the election. Do that by getting the highest count of user polygons for that neighborhood
     * in an election point
     **/
    public function getPeakNumUserNeighborhoods(Neighborhood $neighborhood) {
        $peak_num = 0;
        foreach ($this->getPointElections() as $point_election) {
            $num_votes = $point_election->candidateNeighborhood($neighborhood)->getNumVotes();
            if ($peak_num < $num_votes)
                $peak_num = $num_votes;
        }
        return $peak_num;
    }

    /**
     * return the points that belong to the neighborhood
     */
    public function byNeighborhood(Neighborhood $neighborhood) {
        if (empty($neighborhood))
            throw new \InvalidArgumentException("neighborhood id must be defined");
        $points = array();
        foreach($this->_point_elections as $p) {
            if (!$p->isTie()) {
                if($p->isWinner($neighborhood)) {
                    array_push($points,$p);
                }
            }
        }
        return $points;
    }

    public function heatMapPointsByNeighborhood(Neighborhood $neighborhood) {
        $heatmap_point_elections = array();
        $peakNumForPoints = $this->getPeakNumUserNeighborhoods($neighborhood);
        foreach ($this->getPointElections() as $ep) {
            $cn = $ep->candidateNeighborhood($neighborhood);
            $percentage = $cn->getNumVotes() / $peakNumForPoints;
            array_push($heatmap_point_elections, HeatMapPoint::build( array(
                'neighborhood' => $neighborhood,
                'electionPoint' => $ep,
                'point' => $ep->getPoint(),
                'percentage' => $percentage )));
        }
        return $heatmap_point_elections;
    }
}
