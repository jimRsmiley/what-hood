<?php
namespace Whathood;

use CrEOF\Spatial\PHP\Types\Geometry\Point;

/**
 * store a point and all the user_polygons that contain it.
 * Provide accessor methods for counting them
 */
class ElectionPoint extends \ArrayObject {

    protected $_point;

    protected $_user_polygons;

    protected $_winning_neighborhood_arr;

    protected $units = null;

    public function getPoint() {
        return $this->_point;
    }

    public function getUnits() {
        return $this->units;
    }

    public function __construct(Point $point, $user_polygons) {
        $this->_point = $point;
        $this->_user_polygons = $user_polygons;
    }

    /**
     * create a new election point and have it count it's user polygons
     */
    public static function build(Point $point, $user_polygons) {
        $election_point = new static($point,$user_polygons);
        $election_point->countUserPolygons();
        return $election_point;
    }

    /**
     * was there a tie in this point election
     * @return bool
     */
    public function isTie() {
        return count($this->getWinnerIds()) > 1;
    }

    /*
     * return true if the neighborhood with this id is the winner, or in a tie
     * @param integer - neighborhood id
     * @return boolean
     */
    public function isWinner($neighborhood_id) {
        foreach($this->getWinnerIds() as $test_id) {
            if ($test_id == $neighborhood_id)
                return true;
        }
        return false;
    }

    /**
     * throw exception if there's more or less than one winner
     */
    public function getSingleWinner() {
        $count = count($this->getWinners());
        if ($count < 1)
            throw new \Exception("was expecting one winner, did not get any");
        else if ($count > 1)
            throw new \Exception("was expecting one winner, got more than one");
        else
            return $this->getWinners()[0];
    }

    /**
     * count the user polygons, point the neighborhood_ids in an associative
     * array with the values being the counts of user polygons
     */
    public function countUserPolygons() {
        $arr = array();

        foreach($this->_user_polygons as $up) {
            $n = $up->getNeighborhood();

            if (array_key_exists($n->getId(),$this->_user_polygons)) {
                $this->units[$n->getId()]++;
            }
            else
                $this->units[$n->getId()]= 1;
        }
        asort($this->units);
    }

    public function totalVotes() {
        return $this->getTotalVotes();
    }

    public function getTotalVotes() {
        return count($this->user_polygons);
    }

    /*
     * return the winning unit or units with the most number of votes, return
     * all of the tieing units if they exist
     */
    public function getWinnerIds() {

        $mostVoteIds = array();

        $mostVotes = 0;
        foreach( $this->getUnits() as $n_id => $votes ) {

            // if it's the sole winner, blow out the array and make it the only
            // unit
            if( $votes > $mostVotes ) {
                $mostVoteIds = array();
                $mostVoteIds[] = $n_id;
                $mostVotes = $votes;
            }
            // if it only ties, add it to the mostVoteIds total
            else if( $votes == $mostVotes ) {
                $mostVoteIds[] = $n_id;
            }
        }

        return $mostVoteIds;
    }

    public function hasUnit( $neighborhoodName ) {

        foreach( $this->units as $unit ) {
            if( $unit->getName() == $neighborhoodName ) {
                return true;
            }
        }
        return false;
    }

    public function toArray() {

        $array = array();

        foreach( $this->units as $unit ) {
            array_push( $array, $unit->toArray() );
        }
        return array(
            'neighborhoods' => $array,
            'total_votes'   => $this->totalVotes
        );
    }

    public function getVotes() {
        return $this->votes;
    }

    public function setVotes($votes) {
        $this->votes = $votes;
    }
}
