<?php
namespace Whathood\Election;

use CrEOF\Spatial\PHP\Types\Geometry\Point;
use Whathood\Entity\Neighborhood;

/**
 * store a point and all the user_polygons that contain it.
 * Provide accessor methods for running an election
 *
 *
 * Usage:
 *
 *  $pointElection = new PointElection(array(
 *      'user_polygons' => $user_polygons,
 *      'point'         => $point
 *  ));
 *      
 *
 * see Whathood.Mapper.PointElectionMapper.html#method_generateBorderPolygon for actually building a border

 * @see Whathood.Mapper.PointElectionMapper.html#method_generateBorderPolygon
 */
class PointElection extends \ArrayObject {

    protected $_point;

    protected $_user_polygons;

    protected $_winning_neighborhood_arr;

    // an array of CandidateNeighborhoods
    protected $_candidate_neighborhoods = null;

    protected $_logger = null;

    /**
     * @ignore
     */
    public function setLogger($logger) {
        $this->_logger = $logger;
    }

    /**
     * @ignore
     */
    public function getLogger() {
        return $this->_logger;
    }

    /**
     * @ignore
     */
    public function logger() {
        return $this->getLogger();
    }

    public function getUserPolygons() {
        return $this->_user_polygons;
    }

    public function setUserPolygons($user_polygons) {
        $this->_user_polygons = $user_polygons;
    }

    public function getPoint() {
        return $this->_point;
    }

    public function setPoint($point) {
        $this->_point = $point;
    }

    public function getCandidateNeighborhoods() {
        return $this->_candidate_neighborhoods;
    }

    public function getNumCandidates() {
        return count($this->getCandidateNeighborhoods());
    }

    public function candidateNeighborhood(Neighborhood $n) {
        foreach ($this->getCandidateNeighborhoods() as $cn) {
            if ($cn->getNeighborhood()->getId() == $n->getId()) {
                return $cn;
            }
        }
    }

    public function __construct(array $data = null) {
        if ( ! empty($data) ) {
            $hydrator = new \Zend\Stdlib\Hydrator\ClassMethods();
            $hydrator->hydrate($data, $this);
        }
        $this->_candidate_neighborhoods = array();
    }

    /**
     * create a new election point and have it count it's user polygons
     * @param array data
     * @return PointElection
     */
    public static function build(array $data = null) {

        if (!array_key_exists('logger', $data))
            throw new \InvalidArgumentException("logger must be defined");

        $election_point = new static($data);

        if ( ! $election_point->getPoint() )
            throw new \Exception("point must be defined");
        $election_point->runElection();
        if ( empty($election_point->getCandidateNeighborhoods() ) )
            throw new \Exception("no candidate neighborhoods were found");
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

    public function getNumWinners() {
        return count($this->getWinningCandidates());
    }

    public function getWinningPercentage() {
        if (empty($this->getWinningCandidates())) {
            return 0;
        }
        return $this->getWinningCandidates()[0]->percentage();
    }

    /**
     * throw exception if there's more or less than one winner
     * @return \Whathood\Election\CandidateNeighborhood
     */
    public function getSingleWinner() {
        $count = count($this->getWinningCandidates());
        if ($count < 1)
            throw new \Whathood\Exception("was expecting one winner, did not get any");
        else if ($count > 1)
            throw new \Whathood\Exception("was expecting one winner, got more than one");
        else
            return $this->getWinningCandidates()[0];
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

            if (array_key_exists($n->getId(),$this->_candidate_neighborhoods)) {
                $this->_candidate_neighborhoods[$n->getId()]->increment_vote();
            }
            else {
                $cn = CandidateNeighborhood::build(array(
                    'point_election' => $this,
                    'num_votes'=> 1,
                    'neighborhood'=> $n
                ));
                $this->_candidate_neighborhoods[$n->getId()]=$cn;
            }
        }

        if (0 and $this->getNumCandidates() > 1) {
            print $this->__toString();

            foreach ($this->getCandidateNeighborhoods() as $cn) {
                $this->logger()->info($cn->__toString());
            }
        }

        if (0 and $this->getNumWinners() == 1) {
            $winner = $this->getSingleWinner();

            $percentage = $winner->percentage();
            $this->logger()->debug(
                sprintf("winner is %s with %s percent of the vote",
                    $winner->getNeighborhood()->getName(),
                    $winner->percentage()
                )
            );
            if ($percentage < 100 ) {
                foreach($this->getUserPolygons() as  $up ) {
                    $this->logger()->err("up: ".$up->getNeighborhood()->getName());
                }
                die("dieing now");
            }
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
        $totalVotes = $this->getTotalVotes();
        foreach( $this->getCandidateNeighborhoods() as $cn ) {
            $test_num_votes = $cn->getNumVotes();
            $challenger_name = $cn->getNeighborhood()->getName();
            // if it's the sole winner, blow out the array and make it the only
            // unit
            if( $test_num_votes > $mostVotes ) {
                $mostVoteIds = array();
                $mostVoteCNs = array($cn);
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

    public static function allToArrays($pointElections) {
        $arr = array();
        foreach ($pointElections as $pe) {
            array_push($arr, $pe->toArray());
        }
        return $arr;
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

        $region_arr = array();

        if ($this->getCandidateNeighborhoods()) {
            $region_arr = $this->getRegion()->toArray();
        }

        $single_winner = null;
        if (! $this->isTie() ) {
            $single_winner = $this->getSingleWinner()->getNeighborhood()->toArray();
        }

        return array(
            'region'                    => $region_arr,
            'winners'                   => $winners,
            'candidate_neighborhoods'   => $neighborhoods_array,
            'is_tie'                    => $this->isTie(),
            'single_winner'             => $single_winner,
            'total_votes'               => $this->getTotalVotes(),
            'point' => array(
                'x' => $this->getPoint()->getX(),
                'y' => $this->getPoint()->getY()
            )
        );
    }

    public function __toString() {
        $winner_str = "";
        $winner_arr = array();
        foreach ($this->getWinningCandidates() as $cn) {
            array_push($winner_arr, $cn->getNeighborhood()->getName());
        }
        $winner_str = join(',',$winner_arr);
        return sprintf("point=[%s] num-candidates=%s winning-percentage=%s is-tie=%s num-winners=%s winners=[%s]",
            $this->getPoint()->__toString(),
            $this->getNumCandidates(),
            $this->getWinningPercentage(),
            $this->isTie(),
            $this->getNumWinners(),
            $winner_str
        );
    }
}
