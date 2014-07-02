<?php
namespace Application\Model\HeatMap;

use CrEOF\Spatial\PHP\Types\Geometry\Point as GeoPoint;
/**
 * Description of Point
 *
 * @author Jim Smiley twitter:@jimRsmiley
 */
class Point {
    
    protected $point = null;
    protected $count = null;
    
    public function __construct( $x,$y,$count ) {
        $this->point = new GeoPoint( $x, $y );
        $this->count = $count;
    }
    
    public function toString() {
        $string = sprintf('{lat:%s,lon:%s,value:%s}',
                $this->point->getX(),
                $this->point->getY(),
                $this->count
                );
        
        return $string;
    }
}

?>
