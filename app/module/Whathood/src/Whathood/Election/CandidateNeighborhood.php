<?php

namespace Whathood\Election;

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

    public function getName() {
        return $this->getNeighborhood()->getName();
    }

    // sugar
    public function pointElection() {
        return $this->getPointElection();
    }

    public function getPointElection() {
        return $this->_election_point;
    }

    public function setPointElection($point) {
        $this->_election_point = $point;
    }

    public function getTotalVotes() {
        return $this->getPointElection()->totalVotes();
    }

    // sugar
    public function totalVotes() {
        return $this->getTotalVotes();
    }

    public function getNumVotes() {
        return $this->_num_votes;
    }

    public function setNumVotes($vote_num) {
        $this->_num_votes = $vote_num;
    }

    public function increment_vote() {
        $this->_num_votes++;
    }

    // sugar
    public function numVotes() {
        return $this->getNumVotes();
    }

    public function __construct(array $data = null) {
        if (!empty($data)) {
            $hydrator = new \Zend\Stdlib\Hydrator\ClassMethods();
            $hydrator->hydrate($data,$this);
        }
    }

    public static function build(array $data) {
        $candidate_neighborhood = new CandidateNeighborhood($data);

        if (!$candidate_neighborhood->getPointElection())
            throw new \Exception("pointElection in data must be defined");

        return $candidate_neighborhood;
    }

    public function percentage() {
        return round($this->numVotes() / $this->totalVotes() * 100);
    }

    public function toArray() {
        $array = $this->getNeighborhood()->toArray();

        // append point election data to the neighborhood array
        $array['num_votes'] = $this->getNumVotes();
        $array['total_votes'] = $this->totalVotes();
        $array['percentage'] = $this->percentage();

        return $array;
    }

    public function __toString() {
        return sprintf("name=%s percentage=%s",
            $this->getName(),
            $this->percentage()
        );
    }
}
