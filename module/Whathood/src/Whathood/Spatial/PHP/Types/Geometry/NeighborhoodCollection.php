<?php
namespace Application\Spatial\PHP\Types\Geometry;

use Application\Entity\Neighborhood;
/**
 * Description of NeighborhoodCollection
 *
 * @author Jim Smiley twitter:@jimRsmiley
 */
class NeighborhoodCollection implements \Iterator {
    
    protected $position = null;
    
    protected $neighborhoods = null;
    
    public function __construct( $array = null ) {
        if( is_array( $array ) )
            $this->neighborhoods = $array;
        else
            $this->neighborhoods = array();
    }
    
    public function add( Neighborhood $neighborhood ) {
        array_push( $this->neighborhoods, $neighborhood );
    }
    
    /*
     * return true if the neighborhood exists in the collection
     */
    public function exists( $neighborhoodName, $regionName ) {
        
        foreach( $this->neighborhoods as $n ) {
            
            if( strtolower($n->getName()) == strtolower($neighborhoodName)
                    && strtolower($n->getRegion()->getName()) == strtolower($regionName))
                return true;
        }
        return false;        
    }
    
    public function valid()
    {
        return isset( $this->neighborhoods[$this->position]);
    }
    
    function key() {
        return $this->position;
    }
    
    function current() {
        return $this->neighborhoods[$this->position];
    }
    
    function rewind() {
        $this->position = 0;
    }
    
    function next() {
        ++$this->position;
    }
}

?>
