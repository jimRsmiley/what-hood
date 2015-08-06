<?php
namespace Whathood\Election;

use CrEOF\Spatial\PHP\Types\Geometry\Point;
use Whathood\Entity\Neighborhood;

/**
 * store a point and all the user_polygons that contain it.
 * Provide accessor methods for running an election
 */
class PointElection extends \ArrayObject {

    protected $_point;

    protected $_user_polygons;

    protected $_winning_neighborhood_arr;

    // an array of CandidateNeighborhoods
    protected $_candidate_neighborhoods = null;

    public function getUserPolygons() {
        return $this->_user_polygons;
    }

    public function getPoint() {
        return $this->_point;
    }

    public function getCandidateNeighborhoods() {
        return $this->_candidate_neighborhoods;
    }

    public function candidateNeighborhood(Neighborhood $n) {
        foreach ($this->getCandidateNeighborhoods() as $cn) {
            if ($cn->getNeighborhood()->getId() == $n->getId()) {
                return $cn;
            }
        }
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
        $election_point->runElection();
        return $election_point;
    }

    /**
     * was there a tie in this point election
     * @return bool
     */
    public function isTie() {
        return count($this->getWinningCandidates()) > 1;
    }

    /*
     * return true if the neighborhood with this id is the winner, or in a tie
     * @param integer - neighborhood id
     * @return boolean
     */
    public function isWinner(Neighborhood $neighborhood) {
        foreach($this->getWinningCandidates() as $cn) {
            if ($neighborhood->getId() == $cn->getNeighborhood()->getId())
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
     *
     * @return null
     */
    public function runElection() {
        $arr = array();

        foreach($this->_user_polygons as $up) {
            $n = $up->getNeighborhood();

            if (array_key_exists($n->getId(),$this->_user_polygons)) {
                $this->_candidate_neighborhoods[$n->getId()]->increment_vote();
            }
            else
                $cn = CandidateNeighborhood::build(array(
                    'point_election' => $this,
                    'num_votes'=> 1,
                    'neighborhood'=> $n
                ));
                $this->_candidate_neighborhoods[$n->getId()]=$cn;
        }
    }

    public function totalVotes() {
        return $this->getTotalVotes();
    }

    public function getTotalVotes() {
        return count($this->getUserPolygons());
    }

    /*
     * return the winning CandidateNeighborhood or units with the most number of votes, return
     * all of the tieing units if they exist
     *
     * @return array - an array of CandidateNeighborhoods
     */
    public function getWinningCandidates() {

        $mostVoteCNs = array();

        $mostVotes = 0;
        foreach( $this->getCandidateNeighborhoods() as $cn ) {
            $test_num_votes = $cn->getNumVotes();
            // if it's the sole winner, blow out the array and make it the only
            // unit
            if( $test_num_votes > $mostVotes ) {
                $mostVoteIds = array();
                $mostVoteCNs[] = $cn;
                $mostVotes = $test_num_votes;
            }
            // if it only ties, add it to the mostVoteIds total
            else if( $test_num_votes == $mostVotes ) {
                $mostVoteCNs[] = $cn;
            }
        }

        return $mostVoteCNs;
    }

    /**
     * given a neighborhood id, get the neighborhood object by running through the user polygons
     *
     * @return mixed CandidateNeighborhood object
     */
    public function getCandidateNeighborhoodById($neighborhood_id) {
        if (array_key_exists($neighborhood_id,$this->getCandidateNeighborhoods())) {
            return $this->getCandidateNeighborhoods()[$neighborhood_id];
        }
        else {
            throw new \InvalidArgumentException(
                "candidate neighborhood with neighborhood id $neighborhood_id is not present in user polygons");
        }
    }

    /**
     * return the region for this election
     * do it by grabbing the first CandidateNeighborhood and returning it's region
     * @return mixed Region object
     */
    public function getRegion() {
        if (0 < count($this->getCandidateNeighborhoods())) {
            $n_ids = array_keys($this->getCandidateNeighborhoods());
            return $this->getCandidateNeighborhoods()[$n_ids[0]]->getNeighborhood()->getRegion();
        }
        throw new \Exception("no CandidateNeighborhoods, so cannot get Region");
    }

    public function toArray() {

        $neighborhoods_array = array();

        foreach( $this->getCandidateNeighborhoods()
                            as $neighborhood_id => $votes) {

            array_push(
                $neighborhoods_array,
                $this->getCandidateNeighborhoodById($neighborhood_id)->toArray()
            );
        }

        $winner_cns = $this->getWinningCandidates();
        $winners = array();
        foreach ($winner_cns as $wcn) {
            array_push($winners,$wcn->toArray());
        }

        return array(
            'region'        => $this->getRegion()->toArray(),
            'winners'       => $winners,
            'candidate_neighborhoods' => $neighborhoods_array,
            'total_votes'   => $this->getTotalVotes(),
            'point' => array(
                'x' => $this->getPoint()->getX(),
                'y' => $this->getPoint()->getY()
            )
        );
    }
}
