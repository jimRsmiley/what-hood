<?php
namespace Whathood\Spatial\PHP\Types\Geometry;

use CrEOF\Spatial\PHP\Types\Geometry\LineString as CrEOFLineString;
/**
 * Description of Polygon
 *
 * @author Jim Smiley twitter:@jimRsmiley
 */
class LineString extends CrEOFLineString {

    /*
     * make sure the start point is equal to the end point
     */
    public function close() {
        $startPoint = $this->points[0];
        $endPoint = $this->points[count($this->points)-1];

        if( $startPoint[0] != $endPoint[0] || $startPoint[1] != $endPoint[1] )
            $this->points[] = $this->points[0];
    }

}

?>
