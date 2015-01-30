<?php
namespace Whathood\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
/**
 * @ORM\Entity
 * @ORM\Table(name="whathood_user")
 */
class WhathoodUser extends \ArrayObject {
   
    public function __construct($array=null) {
        
        if( !empty($array) ) {
            $hydrator = new \Zend\Stdlib\Hydrator\ClassMethods();
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

    public function toArray() {
        $hydrator = new \Zend\Stdlib\Hydrator\ClassMethods();
        
        $array = $hydrator->extract($this);
        
        unset( $array['iterator_class'] );
        unset( $array['iterator']);
        unset( $array['flags']);
        unset( $array['array_copy']);
        
        return $array;
    }
    
    public function __toString() {
        return \Zend\Debug\Debug::dump( $this, false );
    }
}
?>
