<?php
namespace Whathood\Entity;

use Doctrine\ORM\Mapping as ORM;
/**
 * Description of Region
 * @ORM\Entity
 * @ORM\Table(name="region",uniqueConstraints={
 *              @ORM\UniqueConstraint(name="region_name_idx", 
 *                  columns={"name"})})
 */
class Region extends \ArrayObject {

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id = null;
    
    /** 
     * @ORM\Column(name="name",type="string") 
     */
    protected $name = null;
    
    /**
     * @ORM\Column(name="center_point",type="point")
     */
    protected $centerPoint;

    /**
     * @ORM\OneToMany(targetEntity="UserPolygon",
     *                              mappedBy="region",cascade="persist")
     */
    protected $neighborhoodPolygons = null;
    
    public function __construct($data=null) {
        if( !empty($data) ) {
            $hydrator = new \Zend\Stdlib\Hydrator\ClassMethods();
            $hydrator->hydrate( $data, $this );
        }
    }
    
    public function getName() {
        return $this->name;
    }
    
    public function setName( $name ) {
        $this->name = $name;
    }
    
    public function setNeighborhoodPolygons( $arrayCollection ) {
        $this->neighborhoodPolygons = $arrayCollection;
    }
    
    public function getNeighborhoodPolygons() {
        return $this->neighborhoodPolygons;
    }
    
    public function getCenterPoint() {
        return $this->centerPoint;
    }
    
    public function setCenterPoint( $centerPoint ) {
        $this->centerPoint = $centerPoint;
    }
    
    public function getId() {
        return $this->id;
    }
    
    public function setId( $id ) {
        $this->id = $id;
    }
    
    public function toArray() {
        $hydrator = new \Zend\Stdlib\Hydrator\ClassMethods();
        
        $array = $hydrator->extract($this);
        
        unset( $array['iterator_class'] );
        unset( $array['iterator']);
        unset( $array['flags']);
        unset( $array['array_copy']);
        
        return $array;
    }
}
?>
