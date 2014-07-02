<?php
namespace Whathood\Spatial\PHP\Types\Geometry;
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
    
    public function toArray() {
        return array(
            'type'      => 'Feature', 
            'geometry'  => $this->geometry->toArray(),
            'properties' => null
        );
    }
}

?>
