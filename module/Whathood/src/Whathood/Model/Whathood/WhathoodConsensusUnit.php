<?php
namespace Whathood\Model\Whathood;
/**
 * I need to store single neighborhood data somewhere, WhathoodResult will store
 * a bunch of these as the result
 *
 * @author Jim Smiley twitter:@jimRsmiley
 */

class WhathoodConsensusUnit extends \ArrayObject {
    
    protected $neighborhoodName;
    
    // votes for this neighborhood
    protected $votes;
    
    // total votes for the point
    protected $totalVotes;
    
    public function __construct($data) {
        if( !empty($data) ) {
            $hydrator = new \Zend\Stdlib\Hydrator\ClassMethods();
            $hydrator->hydrate( $data, $this );
        }
    }

    public function getVotes() {
        return $this->votes;
    }
    
    public function setVotes($votes) {
        $this->votes = $votes;
    }
    
    public function getName() {
        return $this->neighborhoodName;
    }
    
    public function setName( $name ) {
        $this->neighborhoodName = $name;
    }
    
    public function getTotalVotes() {
        return $this->totalVotes;
    }
    
    public function setTotalVotes( $totalVotes ) {
        $this->totalVotes = $totalVotes;
    }
    
    public function addVote() { 
        $this->votes++; 
    }

    public function getVotePercentage() {
        return $this->votes / $this->totalVotes;
    }
    
    public function toArray() {
        return array( 
            'name'  => $this->neighborhoodName,
            'votes' => $this->votes 
        );
    }
}