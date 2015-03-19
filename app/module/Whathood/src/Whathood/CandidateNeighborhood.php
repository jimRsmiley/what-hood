<?php

namespace Whathood;

/**
 * represent a neighborhood up for a point election. The class
 * needs to know how many votes it got, and what percentage
 * of the total votes for the point
 */
class CandidateNeighborhood {

    protected $_vote_num;

    protected $_election_point;

    protected $_neighborhood;

    public function getNeighborhood() {
        return $this->_neighborhood;
    }

    public function setNeighborhood($neighborhood) {
        $this->_neighborhood = $neighborhood;
    }

    public function setPoint($point) {
        $this->_election_point = $point;
    }

    public function getNumVotes() {
        return $this->_num_votes;
    }

    public function setNumVotes($vote_num) {
        $this->_num_votes = $vote_num;
    }

    public function __construct(array $data = null) {
        if (!empty($data)) {
            $hydrator = new \Zend\Stdlib\Hydrator\ClassMethods();
            $hydrator->hydrate($data,$this);
        }
    }

    public static function build(array $data) {
        $candidate_neighborhood = new CandidateNeighborhood($data);
        return $candidate_neighborhood;
    }

    public function incremenet_vote() {
        $this->_vote_num++;
    }

    public function toArray() {
        $array = $this->getNeighborhood()->toArray();
        // append point election data to the neighborhood array
        $array['num_votes'] = $this->getNumVotes();
        return $array;
    }
}
