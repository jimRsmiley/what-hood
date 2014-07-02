<?php
namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Application\Spatial\PHP\Types\Geometry\Polygon;
/**
 * Description of Region
 * @ORM\Entity
 * @ORM\Table(name="region",uniqueConstraints={
 *              @ORM\UniqueConstraint(name="region_name_idx", 
 *                  columns={"name"})})
 */
class Region extends \ArrayObject {
   
    public function __construct($data=null) {
        if( !empty($data) ) {
            $hydrator = new \Zend\Stdlib\Hydrator\ClassMethods();
            $hydrator->hydrate( $data, $this );
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
     * @ORM\Column(name="name",type="string") 
     */
    protected $name = null;
    
    public function getName() {
        return $this->name;
    }
    
    public function setName( $name ) {
        $this->name = $name;
    }
    
    /**
     * @ORM\Column(name="center_point",type="point")
     */
    protected $centerPoint;
    
    public function getCenterPoint() {
        return $this->centerPoint;
    }
    
    public function setCenterPoint( $centerPoint ) {
        $this->centerPoint = $centerPoint;
    }
    
    /**
     * @ORM\Column(name="border_polygon",type="polygon",nullable=true)
     */
    protected $borderPolygon;
    
    public function getBorderPolygon() {
        return $this->borderPolygon;
    }
    
    public function setBorderPolygon( $data ) {
        if( empty( $data ) ) {
            return;
        }
        else if( $data instanceof Polygon )
            $this->borderPolygon = $data;
        else if( is_array( $data ) ) {
            $ring = array();
            foreach( $data['points'] as $point ) {
                $ring[] = new Point( $point['x'],$point['y'] );
            }
            // close the ring
            $ring[] = $ring[0];

            $this->borderPolygon = new Polygon(array($ring));
        }
    }
    
    /**
     * @ORM\OneToMany(targetEntity="NeighborhoodPolygon",
     *                              mappedBy="region",cascade="persist")
     */
    protected $neighborhoodPolygons = null;
    
    public function setNeighborhoodPolygons( ArrayCollection $arrayCollection ) {
        $this->neighborhoodPolygons = $arrayCollection;
    }
    
    public function getNeighborhoodPolygons() {
        return $this->neighborhoodPolygons;
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
