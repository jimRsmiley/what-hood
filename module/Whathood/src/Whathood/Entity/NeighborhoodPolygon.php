<?php

namespace Application\Entity;

// need this even though Netbeans says you don't
use Doctrine\ORM\Mapping as ORM;
use Zend\Stdlib\Hydrator\ClassMethods as ClassMethodHydrator;
use Application\Spatial\PHP\Types\Geometry\Polygon as WhathoodPolygon;
use CrEOF\Spatial\PHP\Types\Geometry\Polygon;
use CrEOF\Spatial\PHP\Types\Geometry\Point;
/**
 * @ORM\Entity
 * @ORM\Table(name="neighborhood_polygon")
 */
class NeighborhoodPolygon extends \ArrayObject {
    
    public function __construct( $array = null ) {
        
        if( $array !== null ) {
            $hydrator = new ClassMethodHydrator();
            $hydrator->hydrate( $array, $this );
        }
    }
    
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id = null;
    
    public function getId() {
        return $this->id;
    }
    
    public function setId( $id ) {
        $this->id = $id;
    }
    
    /**
     * @ORM\ManyToOne(targetEntity="Neighborhood",
     *      inversedBy="neighborhoodPolygons")
     * @ORM\JoinColumn(name="neighborhood_id", referencedColumnName="id",
     *      nullable=false)
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
     * @ORM\ManyToOne(targetEntity="Region",
     *      inversedBy="neighborhoodPolygons")
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
     * @ORM\ManyToOne(targetEntity="WhathoodUser")
     * @ORM\JoinColumn(name="whathood_user_id", referencedColumnName="id",
     *      nullable=false)
     */
    protected $whathoodUser = null;
    
    public function getWhathoodUser() {
        return $this->whathoodUser;
    }
    
    public function setWhathoodUser( $data ) {
        if( is_array( $data ) )
            $this->whathoodUser = new WhathoodUser( $data );
        else if( $data instanceof \Application\Entity\WhathoodUser )
            $this->whathoodUser = $data;
        else
            throw new \InvalidArgumentException('data must be array or User object');
    }
    
    /**
     * @ORM\Column(name="date_time_added")
     */
    protected $dateTimeAdded = null;
    
    public function getDateTimeAdded() {
        return $this->dateTimeAdded;
    }
    
    public function setDateTimeAdded($dateTimeAdded) {
        $this->dateTimeAdded = $dateTimeAdded;
    }
    
    /**
     * @ORM\Column(name="polygon",type="polygon",nullable=false)
     */
    protected $polygon = null;
    
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
    
    /**
     * @ORM\Column(name="authority",type="boolean")
     */
    protected $authority = false;
    
    public function getAuthrotiy() {
        return $this->authority;
    }
    
    public function setAuthority( $authority ) {
        $this->authority = $authority;
    }
    
    /**
     * @ORM\Column(name="is_deleted",type="boolean")
     */
    protected $deleted = false;
    
    public function getDeleted() {
        return $this->deleted;
    }
    
    public function setDeleted( $bool ) {
        $this->deleted = $bool;
    }
    
    /*
     * @ORM\OneToMany(targetEntity="NeighborhoodPolygonVote",mappedBy="neighborhoodPolygon")
     */
    protected $neighborhoodVotes;

    public function getNeighborhoodVotes() {
        return $this->neighborhoodVotes;
    }

    public function setNeighborhoodVotes( $neighborhoodVotes ) {
        $this->neighborhoodVotes =  $neighborhoodVotes;
    }
    
    public function toGeoJsonArray() {
        $hydrator = new \Zend\Stdlib\Hydrator\ClassMethods();
        
        $array = $hydrator->extract($this);

        unset( $array['iterator_class'] );
        unset( $array['iterator']);
        unset( $array['flags']);
        unset( $array['array_copy']);
        unset( $array['polygon']);

        // for geojson, we want to merge the polygon
        $array = array_merge( $array, WhathoodPolygon::toGeoJsonArray( $this->polygon ) );
        
        if( $this->getNeighborhood() != null ) {
            $array['neighborhood'] = $this->getNeighborhood()->toArray();
        }
        
        if( $this->getWhathoodUser() != null ) {
            $array['user'] = $this->getWhathoodUser()->toArray();
        }
        
        return $array;
    }
    
    /*
     * utility function that given an array of neighborhoods, returns a json
     * array
     */
    public static function neighborhoodsToJson( $neighborhoodArray ) {
        
        $jsonArray = array();
        foreach( $neighborhoodArray as $n )
            $jsonArray['neighborhoods'][] = $n->toGeoJsonArray();
            
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
            $neighborhoodPolygons[] = new NeighborhoodPolygon( $neighborhoodArray );
        }
        return $neighborhoodPolygons;
    }
    
    /**
     * if the dateTimeAdded timestamps don't exist, create them using the time now
     * @param \Application\Mapper\ArrayCollection $neighborhoodPolygons
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
     * @param \Application\Mapper\ArrayCollection $neighborhoodPolygons
     */
    public static function setNeighborhoods( 
                                    ArrayCollection $neighborhoodPolygons,
                                    Neighborhood $neighborhood ) {
        
        if( empty( $neighborhoodPolygons ) )
            return;
        
        foreach( $neighborhoodPolygons as $neighborhoodPolygon )
            $neighborhoodPolygon->setNeighborhood( $neighborhood );
    }
}
?>
