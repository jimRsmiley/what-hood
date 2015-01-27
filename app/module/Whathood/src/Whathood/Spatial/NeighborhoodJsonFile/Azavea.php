<?php
namespace Whathood\Spatial\NeighborhoodJsonFile;

use Whathood\Entity\WhathoodUser;
use Whathood\Entity\Neighborhood;
use Whathood\Entity\UserPolygon;
use Whathood\Entity\Region;
use CrEOF\Spatial\PHP\Types\Geometry\Point;
use Whathood\Spatial\PHP\Types\Geometry\Polygon;
use Whathood\Spatial\PHP\Types\Geometry\LineString;
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
            'centerPoint' => new Point(-75.139961,39.97735)
        ));
    }
    
    public function isAuthority() {
        return true;
    }
}

?>
