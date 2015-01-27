<?php
namespace Application\Spatial\PHP\Types\Geometry;

use CrEOF\Spatial\PHP\Types\Geometry\Polygon as CrEOFPolygon;
/**
 * Description of Polygon
 *
 * @author Jim Smiley twitter:@jimRsmiley
 */
class Polygon extends CrEOFPolygon {
    
    public static function toGeoJsonArray(CrEOFPolygon $polygon ) {
        
        $coordinates = array();
        
        foreach( $polygon->getRings() as $ring ) {
            array_push( $coordinates, $ring->toArray() );
        }
        
        $arr = array( 
            'type'      => 'Polygon',
            'coordinates' => $coordinates
        ); 
        
        return $arr;
    }
}

?>
