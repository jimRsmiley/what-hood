<?php

namespace Whathood\Entity;

// need this even though Netbeans says you don't
use Doctrine\ORM\Mapping as ORM;
use Zend\Stdlib\Hydrator\ClassMethods as ClassMethodHydrator;
use Doctrine\Common\Collections\ArrayCollection;
/**
 * @ORM\Entity
 * @ORM\Table(name="neighborhood_polygons_create_event")
 */
class CreateEvent extends \ArrayObject {
    
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id = null;
    
    /**
    * @ORM\Column
    **/
    protected $description = null;

    /**
     * @ORM\Column(name="date_time_created",type="string")
     */
    protected $dateTimeAdded = null;

    /**
    * @ORM\Column(name="test_point_meter_width")
    **/
    protected $testPointMeterWidth = null;

    /**
    * @ORM\Column(name="concave_hull_target_precision")
    **/
    protected $concaveHullTargetPrecision = null;
        
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
    
    public function getDescription() {
        return $this->description;
    }

    public function setDescription( $description ) {
        $this->description = $description;
    }

    public function getDateTimeCreated() {
        return $this->dateTimeAdded;
    }
    
    public function setDateTimeCreated($dateTimeAdded) {
        $this->dateTimeAdded = $dateTimeAdded;
    }

    public function getTestPointMeterWidth() {
        return $this->testPointMeterWidth;
    }

    public function setTestPointMeterWidth($testPointMeterWidth) {
        $this->testPointMeterWidth = $testPointMeterWidth;
    }

    public function getConcaveHullTargetPrecision() {
        return $this->concaveHullTargetPrecision;
    }

    public function setConcaveHullTargetPrecision($concaveHullTargetPrecision) {
        $this->concaveHullTargetPrecision = $concaveHullTargetPrecision;
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
}
?>
