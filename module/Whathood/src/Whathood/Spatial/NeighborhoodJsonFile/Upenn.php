<?php
namespace Application\Spatial\NeighborhoodJsonFile;

use Application\Entity\WhathoodUser;
use Application\Entity\Neighborhood;
use Application\Entity\Region;
use CrEOF\Spatial\PHP\Types\Geometry\Point;
use Application\Spatial\PHP\Types\Geometry\Polygon;
use Application\Spatial\PHP\Types\Geometry\LineString;
/**
 * Description of AzeveaJson
 *
 * @author Jim Smiley twitter:@jimRsmiley
 */
class Upenn extends NeighborhoodJsonFile {
    
    public function getWhathoodUser() {
        if( $this->whathoodUser == null ) {
            return new WhathoodUser( array(
                'userName'  => 'University of Pennsylvania',
            ));
        }
        
        return $this->whathoodUser;
    }
    
    public function getRegion() {
        return new Region( array( 
            'name' => 'Philadelphia',
            'centerPoint' => new Point(39.97735,-75.139961)
            ));
    }

    public function isAuthority() {
        return false;
    }
}

?>
