<?php
namespace Application\Model;

class Vote {

    protected $upStrings = array( '1','up','yes' );
    protected $downStrings = array( '-1','down','no' );
    
    // can be 1 or -1
    protected $castvote;
    
    public function __construct( $voteStr ) {
        $this->castVote = $this->getVote($voteStr);
    }
    
    public function isUp() {
        if( $this->voteCast == 1 )
            return true;
        return false;
    }
    
    public function getDirection() {
        
        if( $this->isUp() ) {
            return 'up';
        }
        else {
            return 'down';
        }
    }
    
    public function getCastVote() {
        return $this->castVote;
    }
    
    public function getVote($voteStr) {
        
        $lcVoteStr = strtolower($voteStr);
        
        foreach( $this->upStrings as $upStr ) {
            if( $upStr == $lcVoteStr )
                return 1;
        }
        
        foreach( $this->downStrings as $downStr ) {
            if( $downStr == $lcVoteStr )
                return -1;
        }
        
        throw new \InvalidArgumentException("cannot figure out vote string " 
                                                                . $voteStr );
    }
    
    public function __toString() {
        return (string)$this->castVote;
    }
}
?>
