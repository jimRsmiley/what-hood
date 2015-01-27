<?php

namespace Whathood\Entity;

// need this even though Netbeans says you don't
use Doctrine\ORM\Mapping as ORM;
use Zend\Stdlib\Hydrator\ClassMethods as ClassMethodHydrator;
use Whathood\Spatial\PHP\Types\Geometry\Polygon as WhathoodPolygon;
use CrEOF\Spatial\PHP\Types\Geometry\Polygon;
use CrEOF\Spatial\PHP\Types\Geometry\Point;
/**
 * @ORM\Entity
 * @ORM\Table(name="neighborhood_polygon",
 * uniqueConstraints={
 *   @ORM\UniqueConstraint(name="neighborhood_polygon_set_number_idx",columns={"neighborhood_id","set_number"})
 * }
 * )
 */
class NeighborhoodPolygon extends \ArrayObject {
    
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id = null;
    
    /**
     * @ORM\ManyToOne(targetEntity="Neighborhood",
     *      inversedBy="neighborhoodPolygons")
     * @ORM\JoinColumn(name="neighborhood_id", referencedColumnName="id",
     *      nullable=false)
     */
    protected $neighborhood = null;
    
    /**
     * @ORM\Column(name="polygon",type="polygon",nullable=false)
     */
    protected $polygon = null;
    
    /**
     * @ORM\Column(name="num_user_polygons",type="integer",nullable=false)
     */
    protected $numUserPolygons = null;
    
    /**
     * @ORM\Column(name="set_number",type="integer",nullable=false)
     */
    protected $setNumber = null;
    
    /** 
     * @ORM\Column(name="timestamp",type="datetimetz",nullable=false)
     */
    protected $timestamp = null;
    
    
    public function __construct( $array = null ) {
        
        if( $array !== null ) {
            $hydrator = new ClassMethodHydrator();
            $hydrator->hydrate( $array, $this );
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
    
    public function setPolygon(  $data ) {
        if( $data instanceof Polygon )
            $this->polygon = $data;
        else if( is_array( $data ) ) {
            $ring = array();
            foreach( $data['points'] as $point ) {
                $ring[] = new Point( $point['x'],$point['y'] );
            }
            // close the ring
            $ring[] = $ring[0];

            $this->polygon = new Polygon(array($ring));
        }
    }
    
    public function getPolygon() {
        return $this->polygon;
    }
    
    public function setSetNumber( $setNumber ) {
        $this->setNumber = $setNumber;
    }
    
    public function getSetNumber() {
        return $this->setNumber;
    }
    
    public function toArray() {
        $hydrator = new \Zend\Stdlib\Hydrator\ClassMethods();
        
        $array = $hydrator->extract($this);

        unset( $array['iterator_class'] );
        unset( $array['iterator']);
        unset( $array['flags']);
        unset( $array['array_copy']);
        unset( $array['polygon']);

        // for geojson, we want to merge the polygon
        $array = array_merge( $array, $this->polygonToGeoJsonArray( $this->polygon ) );
        
        if( $this->getNeighborhood() != null ) {
            $array['neighborhood'] = $this->getNeighborhood()->toArray();
        }
        
        return $array;
    }
    
    public static function polygonToGeoJsonArray( $polygon ) {
        
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
    
    public static function fromGeoJsonArray( $array ) {
        
        $type = $array['type'];
        $coordinates = $array['coordinates'];
        
        $polygon = new Polygon( array( new LineString( $coordinates ) ) );
        
        return $polgyon;
    }
    
    /*
     * utility function that given an array of neighborhoods, returns a json
     * array
     */
    public static function neighborhoodsToJson( $neighborhoodArray ) {
        
        $jsonArray = array();
        foreach( $neighborhoodArray as $n )
            $jsonArray['neighborhoods'][] = $n->toArray();
            
        return  \Zend\Json\Json::encode($jsonArray);
    }
    
    /*
     * utility function that given an array of neighborhoods, returns a json
     * array
     */
    public static function jsonToNeighborhoodPolygons( $json ) {
        
        $array = \Zend\Json\Json::decode( $json, \Zend\Json\Json::TYPE_ARRAY );
        
        $neighborhoodPolygons = array();
        foreach( $array['neighborhoods'] as $neighborhoodArray ) {
            $neighborhoodPolygons[] = new UserPolygon( $neighborhoodArray );
        }
        return $neighborhoodPolygons;
    }
    
    /**
     * if the dateTimeAdded timestamps don't exist, create them using the time now
     * @param \Whathood\Mapper\ArrayCollection $neighborhoodPolygons
     */
    public static function setTimes( 
                                    ArrayCollection $neighborhoodPolygons ) {
        
        if( empty( $neighborhoodPolygons ) )
            return;
        
        foreach( $neighborhoodPolygons as $neighborhoodPolygon )
            $neighborhoodPolygon->setDateTimeAdded( date("Y-m-d H:i:s") );
        
    }
    
    /**
     * if the dateTimeAdded timestamps don't exist, create them using the time now
     * @param \Whathood\Mapper\ArrayCollection $neighborhoodPolygons
     */
    public static function setNeighborhoods( 
                                    $neighborhoodPolygons,
                                    Neighborhood $neighborhood ) {
        
        if( empty( $neighborhoodPolygons ) )
            return;
        
        foreach( $neighborhoodPolygons as $neighborhoodPolygon )
            $neighborhoodPolygon->setNeighborhood( $neighborhood );
    }
}
?>
