<?php

/**
 * Description of Whathood
 *
 * @author Jim Smiley twitter:@jimRsmiley
 */
class Whathood {
    
    public function getConsensus( Point $point ) {
        $neighborhoods = $this->neighborhoodMapper()->getNeghborhoodPolygonsByPoint($point);
        $consensus = new Consensus( $neighborhoods );
        
    }
    
    public function neighborhoodMapper() {
        return $this->sm->get('Whathood\Mapper\Neighborhood');
    }
}

?>
