<?php
namespace Whathood\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
/**
 * @ORM\Entity
 * @ORM\Table(name="whathood_user",
 * uniqueConstraints={
 *   @ORM\UniqueConstraint(name="whathood_user_idx",columns={"user_name"})
 * }
 * )
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
    
    /**
     * @ORM\Column(name="facebook_user_id",type="bigint",nullable=true)
     */
    protected $facebookUserId = null;
    
    public function setFacebookUserId( $facebookUserId ) {
        $this->facebookUserId = $facebookUserId;
    }
    
    public function getFacebookUserId() {
        return $this->facebookUserId;
    }
    
    /** 
     * @ORM\Column(name="user_name",type="string") 
     */
    protected $userName = null;
    
    public function getUserName() {
        return $this->userName;
    }
    
    public function setUserName( $userName ) {
        $this->userName = $userName;
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
    
    public function __toString() {
        return \Zend\Debug\Debug::dump( $this, false );
    }
}
?>
