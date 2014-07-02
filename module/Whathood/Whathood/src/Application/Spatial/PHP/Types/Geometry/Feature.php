<?php
namespace Application\Spatial\PHP\Types\Geometry;
/**
 * Description of FeatureCollection
 *
 * @author Jim Smiley twitter:@jimRsmiley
 */
class Feature {
    
    protected $geometry = null;
    protected $properties = null;
    
    public function __construct( $geometry ) {
        $this->geometry = $geometry;
        $this->properties = array();
    }
    
    public function toGeoJsonArray() {
        return array(
            'type'      => 'Feature', 
            'geometry'  => $this->geometry->toGeoJsonArray(),
            'properties' => null
        );
    }
}

?>
