<?php

namespace Application\Model;
/**
 * Description of NeighborhoodPolygonVoteCollection
 *
 * @author Jim Smiley twitter:@jimRsmiley
 */
class NeighborhoodPolygonVoteCollection {
    
    protected $neighborhoodPolygon;
    protected $neighborhoodPolygonVotes;
    protected $upVoteCount;
    protected $downVoteCount;
    
    public function __construct( $data ) {
        
        $hydrator = new \Zend\Stdlib\Hydrator\ClassMethods(false);
        $hydrator->hydrate($data, $this);
        
        // count the votes
        $this->countVotes();
    }
    public function toArray() {
        return array(
            'neighborhoodPolygon' => $this->getNeighborhoodPolygon()
                                                ->getNeighborhood()->toArray(),
            'upCount'   => $this->getUpCount(),
            'downCount' => $this->getDownCount()
        );
    }
    
    public function getNeighborhoodPolygon() {
        return $this->neighborhoodPolygon;
    }
    
    public function setNeighborhoodPolygon( $neighborhoodPolygon ) {
        $this->neighborhoodPolygon = $neighborhoodPolygon;
    }
    
    public function getNeighborhoodPolygonVotes() {
        return $this->neighborhoodPolygonVotes;
    }
    
    public function setNeighborhoodPolygonVotes( $neighborhoodPolygonVotes ) {
        $this->neighborhoodPolygonVotes = $neighborhoodPolygonVotes;
    }
    
    
    public function countVotes() {
        
        $upVotes = 0;
        $downVotes = 0;
        
        foreach( $this->neighborhoodPolygonVotes as $vote ) {
            if( $vote->isUp() ) {
                $upVotes++;
            } else {
                $downVotes++;
            }
        }
        $this->upVoteCount = $upVotes;
        $this->downVoteCount = $downVotes;
    }
    
    public function getUpCount() {
        return $this->upVoteCount;
    }
    
    public function getDownCount() {
        return $this->downVoteCount;
    }
}

?>
