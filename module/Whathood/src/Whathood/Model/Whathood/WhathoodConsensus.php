<?php
namespace Application\Model\Whathood;
/**
 * I need to store single neighborhood data somewhere, WhathoodResult will store
 * a bunch of these as the result
 *
 * @author Jim Smiley twitter:@jimRsmiley
 */

class WhathoodConsensus {
    
    protected $units = null;
    protected $totalVotes = null;
    
    public function __construct( $neighborhoods = null ) {
        $this->units = array();
        $this->totalVotes = 0;
        
        if( $neighborhoods != null ) {
            $this->addNeighborhoods( $neighborhoods );
        }
    }
    
    public function getTotalVotes() {
        return $this->totalVotes;
    }
    
    public function addNeighborhoods( $neighborhoodPolygons ) {
        foreach( $neighborhoodPolygons as $neighborhoodPolygon ) {
            $nName = $neighborhoodPolygon->getNeighborhood()->getName();
        
            if( $this->hasUnit($nName) )
                $this->getUnitByName( $nName )->addVote();
            else
                $this->addUnit( $neighborhoodPolygon );

            $this->totalVotes++;
        }
        
        /*
         * now set the total votes for the units
         */
        foreach( $this->units as $unit )
            $unit->setTotalVotes( $this->totalVotes );
    }
    
    public function getUnits() {
        return $this->units;
    }
    
    public function getUnitByName( $neighborhoodName ) {
        foreach( $this->units as $unit ) {
            if( $unit->getName() == $neighborhoodName ) {
                return $unit;
            }
        }
        return null;
    }
    
    public function getVoteNum( $neighborhoodName ) {
        
        
        foreach( $this->getUnits() as $unit ) {
            if( $unit->getName() == $neighborhoodName ) {
                return $unit->getVotes();
            }
        }
        
        return 0;
    }
    
    /*
     * return the winning unit or units with the most number of votes, return
     * all of the tieing units if they exist
     */
    public function getWinnerUnits() {
        
        $mostVoteUnits = array();
        
        $mostVotes = 0;
        foreach( $this->getUnits() as $unit ) {
            $votes = $unit->getVotes();
            
            // if it's the sole winner, blow out the array and make it the only
            // unit
            if( $votes > $mostVotes ) {
  //              print "new most votes". $mostVotes."\n";
                $mostVoteUnits = array();
                $mostVoteUnits[] = $unit;
                $mostVotes = $votes;
            } 
            // if it only ties, add it to the mostVoteUnits total
            else if( $votes == $mostVotes ) {
//                print "we have a tie\n";
                $mostVoteUnits[] = $unit;
            }
        }
        
        return $mostVoteUnits;
    }
    
    public function hasUnit( $neighborhoodName ) {
        
        foreach( $this->units as $unit ) {
            if( $unit->getName() == $neighborhoodName ) {
                return true;
            }
        }
        return false;
    }
    
    public function addUnit( $neighborhoodPolygon ) {
        $this->units[] = new WhathoodConsensusUnit( array( 
            'neighborhood' => $neighborhoodPolygon,
            'name'  => $neighborhoodPolygon->getNeighborhood()->getName(),
            'votes' => 1
        ));
        
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
}


?>
