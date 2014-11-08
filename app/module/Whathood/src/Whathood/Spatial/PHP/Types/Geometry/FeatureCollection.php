<?php
namespace Whathood\Spatial\PHP\Types\Geometry;
/**
 * Description of FeatureCollection
 *
 * @author Jim Smiley twitter:@jimRsmiley
 */
class FeatureCollection {
    
    protected $geometry = null;
    
    public function __construct() {
        $this->features = array();
    }
    
    public function addGeometry( $feature ) {
        
        if( $feature instanceof Feature ) {
            array_push( $this->features, $feature );
        }
        else {
            array_push( $this->features, new Feature( $feature ) );
        }
    }
    
    public function toArray() {
        
        $featuresArray = array();
        
        foreach( $this->features as $f ) {
            array_push( $featuresArray, $f->toArray() );
        }        
        return array(
            'type'      => 'FeatureCollection', 
            'features'  => $featuresArray
        );
        
    }
}

?>
