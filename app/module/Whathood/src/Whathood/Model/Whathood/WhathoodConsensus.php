<?php
namespace Whathood\Model\Whathood;

/**
 * For one point, I need to store single neighborhood data somewhere, WhathoodResult will store
 * a bunch of these as the result
 *
 * @author Jim Smiley twitter:@jimRsmiley
 */

class WhathoodConsensus {


    public function __construct( $user_polygons = null ) {
        $this->units = array();
        $this->totalVotes = 0;

        if( $user_polygons != null ) {
            $this->addNeighborhoods( $user_polygons );
        }
    }

}


?>
