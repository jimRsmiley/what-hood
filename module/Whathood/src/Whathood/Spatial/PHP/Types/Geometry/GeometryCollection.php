<?php
namespace Application\Spatial\PHP\Types\Geometry;
/**
 * Description of GeometryCollection
 *
 * @author Jim Smiley twitter:@jimRsmiley
 */
class GeometryCollection {
    
    protected $geometry = null;
    
    public function __construct() {
        $this->geometry = array();
    }
    
    public function addGeometry( $feature ) {
        array_push( $this->geometry, $feature );
    }
    
    public function toArray() {
        
        $geometries = array();
        
        foreach( $this->geometry as $f ) {
            array_push( $geometries, $f->toArray() );
        }        
        return array(
            'type'      => 'GeometryCollection', 
            'geometries'  => $geometries
        );
        
    }
}

?>
