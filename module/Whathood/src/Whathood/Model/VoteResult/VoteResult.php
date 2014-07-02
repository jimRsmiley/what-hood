<?php
namespace Whathood\Model\VoteResult;

use Whathood\Entity\NeighborhoodPolygonVote;

/**
 * when querying whathood, we need a container that will hold the 
 * WhathoodVoteResult, response
 *
 * @author Jim Smiley twitter:@jimRsmiley
 */
class VoteResult {
    
    protected $thisUsersVote;
    protected $allVotesForNeighborhoodPolygon;
    
    public function __construct($data=null) {
        
        if( $data != null ) {
            $hydrator = new \Zend\Stdlib\Hydrator\ClassMethods();
            $hydrator->hydrate($data, $this);
        }
    }
    
    public function setThisUsersVote( NeighborhoodPolygonVote $thisUsersVote ) {
        $this->thisUsersVote = $thisUsersVote;
    }
    
    public function setAllVotesForNeighborhoodPolygon( 
                                            $allVotesForNeighborhoodPolygon ) {
        
        $this->allVotesForNeighborhoodPolygon = $allVotesForNeighborhoodPolygon;
    }
    
    public function toArray() {
        
        $array = array();
        
        if( $this->thisUsersVote != null )
            $array['thisUsersVote'] = $this->thisUsersVote->toArray();
        else
            $array['thisUsersVote'] = null;
        
        $array['allVotes'] = $this->allVotesForNeighborhoodPolygon->toArray();
        
        return $array;
    }
}

?>
