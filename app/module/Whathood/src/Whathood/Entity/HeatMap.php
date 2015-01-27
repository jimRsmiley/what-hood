<?php
namespace Application\Entity;

// need this even though Netbeans says you don't
use Doctrine\ORM\Mapping as ORM;
use Zend\Stdlib\Hydrator\ClassMethods as ClassMethodHydrator;
use Application\Entity\Region;
use CrEOF\Spatial\PHP\Types\Geometry\Polygon;
use CrEOF\Spatial\PHP\Types\Geometry\Point;
/**
 * @ORM\Entity
 * @ORM\Table(name="heat_map")
 */
class HeatMap {
    
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
    public function getId() {
        return $this->id;
    }
    
    public function setId( $id ) {
        $this->id = $id;
    }
    
    /**
     * @ORM\ManyToOne(targetEntity="Neighborhood")
     * @ORM\JoinColumn(name="neighborhood_id", referencedColumnName="id")
     */
    protected $neighborhood = null;
    
    public function getNeighborhood() {
        return $this->neighborhood;
    }
    
    public function setNeighborhood( $data ) {
        if( is_array( $data ) )
            $this->neighborhood = new Neighborhood( $data );
        else if( $data instanceof \Application\Entity\Neighborhood )
            $this->neighborhood = $data;
        else
            throw new \InvalidArgumentException(
                                'data must be array or Neighborhood object');
    }
    
    
    /**
     * @ORM\ManyToMany(targetEntity="NeighborhoodPolygon", inversedBy="heatMaps")
     * @ORM\JoinTable(name="neighborhood_polygons_heat_maps")
     */
    protected $neighborhoodPolygons;
    
    public function getNeighborhoodPolygons() {
        return $this->neighborhoodPolygons;
    }
    
    public function setNeighborhoodPolygons($neighborhoodPolygons) {
        $this->neighborhoodPolygons = $neighborhoodPolygons;
    }
    
    /**
     * @ORM\ManyToOne(targetEntity="Region",
     *      inversedBy="neighborhoodPolygons",cascade="persist")
     * @ORM\JoinColumn(name="region_id", referencedColumnName="id",
     *      nullable=false)
     */
    protected $region = null;
    
    public function getRegion() {
        return $this->region;
    }
    
    public function setRegion( $data ) {
        if( is_array( $data ) )
            $this->region = new Region( $data );
        else if( $data instanceof \Application\Entity\Region )
            $this->region = $data;
        else
            throw new \InvalidArgumentException(
                                'data must be array or Region object');
    }
    
    /**
     * @ORM\Column(name="heat_map_js_data",type="text")
     */
    protected $heatMapJsData;
    
    public function getHeatMapJsData() {
        return $this->heatMapJsData;
    }
    
    public function setHeatMapJsData($str) {
        $this->heatMapJsData = $str;
    }

/**
     * @ORM\Column(name="max",type="integer")
     */
    protected $max;
    
    public function getMax() {
        return $this->max;
    }
    
    public function setMax($max) {
        $this->max = $max;
    }
    
    /**
     * @ORM\Column(name="date_time_added",type="string")
     */
    protected $dateTimeAdded = null;
    
    public function getDateTimeAdded() {
        return $this->dateTimeAdded;
    }
    
    public function setDateTimeAdded( $dateTimeAdded) {
        $this->dateTimeAdded = $dateTimeAdded;
    }
    
    protected $points;
    
    public function __construct( $data ) {
        $hydrator = new \Zend\Stdlib\Hydrator\ClassMethods();
        $hydrator->hydrate($data, $this);
    }

	public function getPoints(){
		return $this->points;
	}

	public function setPoints($points){
		$this->points = $points;
        $this->setHeatMapJsData($this->toHeatMapJsDataString($points));
	}
    
    public function toHeatMapJsDataString( $points ) {
        $str = '[';
        $pointStrings = array();
        foreach( $this->points as $point ) {
            $pointStrings[] = $point->toString();
        }
        $str .= implode( ',',$pointStrings );
        
        $str .= ']';
        
        return $str;
    }
    public function toString() {
        $str = '[';
        
        $pointStrings = array();
        foreach( $this->points as $point ) {
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
