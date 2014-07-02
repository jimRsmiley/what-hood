<?php
namespace Application\Spatial\NeighborhoodJsonFile;

use Application\Entity\WhathoodUser;
use Application\Entity\Neighborhood;
use Application\Entity\NeighborhoodPolygon;
use Application\Entity\Region;
use CrEOF\Spatial\PHP\Types\Geometry\Point;
use Application\Spatial\PHP\Types\Geometry\Polygon;
use Application\Spatial\PHP\Types\Geometry\LineString;
/**
 * Description of AzeveaJson
 *
 * @author Jim Smiley twitter:@jimRsmiley
 */
class Azavea extends NeighborhoodJsonFile {
    
    public function getWhathoodUser() {
        
        if( $this->whathoodUser == null ) {
            $this->whathoodUser = new WhathoodUser( array(
            'userName'  => 'Azavea',
            'authority' => true
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
        return true;
    }
}

?>
