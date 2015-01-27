<?php
namespace Whathood\Model;

// need this even though Netbeans says you don't
use Doctrine\ORM\Mapping as ORM;
use Zend\Stdlib\Hydrator\ClassMethods as ClassMethodHydrator;
use Whathood\Entity\Region;
use CrEOF\Spatial\PHP\Types\Geometry\Polygon;
use CrEOF\Spatial\PHP\Types\Geometry\Point;

class HeatMap {
    

    protected $id;
    

    protected $neighborhood = null;

    protected $neighborhoodPolygons;

    protected $region = null;
    

    protected $maxWeight;

    protected $dateTimeAdded = null;
    
    protected $heatMapPoints;
    
    public function __construct( $data = null) {
        
        if( null !== $data ) {
            $hydrator = new \Zend\Stdlib\Hydrator\ClassMethods();
            $hydrator->hydrate($data, $this);
        }
    }
    
    public function getId() {
        return $this->id;
    }
    
    public function setId( $id ) {
        $this->id = $id;
    }
    
    public function getNeighborhood() {
        return $this->neighborhood;
    }
    
    public function setNeighborhood( $data ) {
        if( is_array( $data ) )
            $this->neighborhood = new Neighborhood( $data );
        else if( $data instanceof \Whathood\Entity\Neighborhood )
            $this->neighborhood = $data;
        else
            throw new \InvalidArgumentException(
                                'data must be array or Neighborhood object');
    }
    
    public function getNeighborhoodPolygons() {
        return $this->neighborhoodPolygons;
    }
    
    public function setNeighborhoodPolygons($neighborhoodPolygons) {
        $this->neighborhoodPolygons = $neighborhoodPolygons;
    }
    
    public function getRegion() {
        return $this->region;
    }
    
    public function setRegion( $data ) {
        if( is_array( $data ) )
            $this->region = new Region( $data );
        else if( $data instanceof \Whathood\Entity\Region )
            $this->region = $data;
        else
            throw new \InvalidArgumentException(
                                'data must be array or Region object');
    }

    public function getMax() {
        
        if( $this->maxWeight == null ) {
            $maxWeight = 0;
            foreach( $this->getHeatMapPoints() as $point ) {
                if( $point->getStrengthOfIdentity() > $maxWeight )
                    $maxWeight = $point->getStrengthOfIdentity();
            }
            $this->maxWeight = $maxWeight;
        }
        return $this->maxWeight;
    }
    
    public function setMax($max) {
        $this->maxWeight = $max;
    }
    
    public function getDateTimeAdded() {
        return $this->dateTimeAdded;
    }
    
    public function setDateTimeAdded( $dateTimeAdded) {
        $this->dateTimeAdded = $dateTimeAdded;
    }
    
	public function getHeatMapPoints(){
		return $this->heatMapPoints;
	}

	public function setHeatMapPoints($points){
		$this->heatMapPoints = $points;
	}
    
    public function toHeatMapJsDataString() {
        $str = '[';
        $pointStrings = array();
        foreach( $this->heatMapPoints as $point ) {
            $pointStrings[] = $point->toString();
        }
        $str .= implode( ',',$pointStrings );
        
        $str .= ']';
        
        return $str;
    }
    
    public function toString() {
        $str = '[';
        
        $pointStrings = array();
        foreach( $this->heatMapPoints as $point ) {
            $pointStrings[] = $point->toString();
        }
        $str .= implode( ',',$pointStrings );
        $str .= ']';
        
        return $str;
    }
    
    public function toJsonArray() {
        $array = array(
            'neighborhoodName' => $this->neighborhoodName,
            'regionName'       => $this->regionName,
            'heatMapJsData'    => $this->heatMapJsData
        );
        return $array;
    }
}

?>
