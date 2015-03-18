<?php

namespace Whathood;

/**
 * store whathood consensus units and provide easy retreival methods
 *
 */
class ElectionPointCollection {

    protected $points;

    public function __construct($points) {
        $this->_points = $points;
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
}
