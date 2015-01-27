<?php
namespace Whathood\Entity;

// need this even though Netbeans says you don't
use Doctrine\ORM\Mapping as ORM;
use Zend\Stdlib\Hydrator\ClassMethods as ClassMethodHydrator;
use Whathood\Entity\Region;
use CrEOF\Spatial\PHP\Types\Geometry\Polygon;
use CrEOF\Spatial\PHP\Types\Geometry\Point;
/**
 * we need to store heat map points, but they need to have weight associated
 * 
 * @ORM\Entity
 * @ORM\Table(name="neighborhood_heat_map_point")
 */
class NeighborhoodHeatMapPoint {
   
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
    /**
     * @ORM\Column(name="point",type="point")
     * @var point
     */
    protected $point;
    
    /**
     * @ORM\Column(name="strength_of_identity",type="float")
     */
    protected $strengthOfIdentity;
    
    /**
     * @ORM\ManyToOne(targetEntity="Neighborhood",inversedBy="heatMapPoints",fetch="LAZY",cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="neighborhood_id")
     */
    protected $neighborhood = null;
    
    /**
     * @ORM\Column(name="set_num",type="integer")
     */
    protected $setNumber;

    public function __construct( $data = null ) {
        
        if( $data !== null ) {
            $hydrator = new \Zend\Stdlib\Hydrator\ClassMethods();
            $hydrator->hydrate($data,$this);
        }
    }
    
    public function getId() {
        return $this->id;
    }
    
    public function setId( $id ) {
        $this->id = $id;
    }
    
    public function getPoint() {
        return $this->point;
    }
    
    public function setPoint( $point ) {
        $this->point = $point;
    }
    
    public function getStrengthOfIdentity() {
        return $this->strengthOfIdentity;
    }
    
    public function setStrengthOfIdentity( $weight ) {
        if( $weight < 0 || $weight > 1 )
            throw new \InvalidArgumentException("weight must be between 0 and 1");
        $this->strengthOfIdentity = $weight;
    }
    
    public function setNeighborhood( $neighborhood) {
        $this->neighborhood = $neighborhood;
    }
    
    public function getNeighborhood() {
        return $this->neighborhood;
    }
    
    public function getSetNumber() {
        return $this->setNumber;
    }
   
    public function setSetNumber( $setNumber ) {
        $this->setNumber = $setNumber;
    }
    
    public function toString() {
        $string = sprintf('{lat:%s,lon:%s,value:%s}',
                $this->point->getY(),
                $this->point->getX(),
                $this->getStrengthOfIdentity()
                );
        
        return $string;
    }
}