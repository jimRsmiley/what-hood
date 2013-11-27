<?php

/**
 * Description of Whathood
 *
 * @author Jim Smiley twitter:@jimRsmiley
 */
class Whathood {
    
    public function getConsensus( Point $point ) {
        $neighborhoods = $this->neighborhoodMapper()->byPoint($point);
        $consensus = new Consensus( $neighborhoods );
        
    }
    
    public function neighborhoodMapper() {
        return $this->sm->get('Application\Mapper\Neighborhood');
    }
}

?>
