<?php

namespace Application\Entity;

// need this even though Netbeans says you don't
use Doctrine\ORM\Mapping as ORM;
use Zend\Stdlib\Hydrator\ClassMethods as ClassMethodHydrator;
use Doctrine\Common\Collections\ArrayCollection;
/**
 * @ORM\Entity
 * @ORM\Table(name="neighborhood",uniqueConstraints={
 *              @ORM\UniqueConstraint(name="name_region_idx", 
 *                  columns={"name","region_id"})})
 */
class Neighborhood extends \ArrayObject {
    
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
     * @ORM\Column(name="name")
     */
    protected $name = null;
    
    public function getName() {
        return $this->name;
    }

    public function setName( $name ) {
        $this->name = $name;
    }
    
    /**
     * @ORM\ManyToOne(targetEntity="Application\Entity\Region",cascade="persist")
     * @ORM\JoinColumn(name="region_id", referencedColumnName="id",nullable=false)
     */
    protected $region = null;

    public function getRegion() {
        return $this->region;
    }

    public function setRegion( $data ) {
        if( is_array($data) )
            $this->region = new Region($data);
        else if( $data instanceof Region )
            $this->region = $data;
        else
            throw new \InvalidArgumentException( 
                                    'data must be array or Region object' );
    }
    
    /**
     * @ORM\Column(name="date_time_added",type="string")
     */
    protected $dateTimeAdded = null;
    
    public function getDateTimeAdded() {
        return $this->dateTimeAdded;
    }
    
    public function setDateTimeAdded($dateTimeAdded) {
        $this->dateTimeAdded = $dateTimeAdded;
    }
    
    /**
     * @ORM\OneToMany(targetEntity="NeighborhoodPolygon",
     *                              mappedBy="neighborhood",cascade="persist")
     */
    protected $neighborhoodPolygons = null;
    
    public function setNeighborhoodPolygons( ArrayCollection $arrayCollection ) {
        $this->neighborhoodPolygons = $arrayCollection;
    }
    
    public function getNeighbhorhoodPolygons() {
        return $this->neighborhoodPolygons;
    }
        
    public function toArray() {
        $hydrator = new \Zend\Stdlib\Hydrator\ClassMethods();
        
        $array = $hydrator->extract($this);

        unset( $array['iterator_class'] );
        unset( $array['iterator']);
        unset( $array['flags']);
        unset( $array['array_copy']);
        unset( $array['polygon']);

        if( $this->getRegion() != null ) {
            $array['region'] = $this->getRegion()->toArray();
        }
        
        return $array;
    }
    
    /*
     * utility function that given an array of neighborhoods, returns a json
     * array
     */
    public static function asdfneighborhoodsToJson( $neighborhoodArray ) {
        
        $jsonArray = array();
        foreach( $neighborhoodArray as $n )
            $jsonArray['neighborhoods'][] = $n->toArray();
            
        return  \Zend\Json\Json::encode($jsonArray);
    }
    
    /*
     * utility function that given an array of neighborhoods, returns a json
     * array
     */
    public static function asdfjsonToNeighborhoods( $json ) {
        
        $array = \Zend\Json\Json::decode( $json, \Zend\Json\Json::TYPE_ARRAY );
        
        $neighborhoods = array();
        foreach( $array['neighborhoods'] as $neighborhoodArray ) {
            $neighborhoods[] = new Neighborhood( $neighborhoodArray );
        }
        return $neighborhoods;
    }
}
?>
