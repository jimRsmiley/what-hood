<?php
namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
/**
 * @ORM\Entity
 * @ORM\Table(name="whathood_user",uniqueConstraints={
 *              @ORM\UniqueConstraint(name="whathood_user_idx", 
 *                  columns={"user_name"})})
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
     * @ORM\OneToOne(targetEntity="Application\Entity\FacebookUser",cascade="persist")
     * @ORM\JoinColumn(name="facebook_user_id", referencedColumnName="id",nullable=true)
     */
    protected $facebookUser = null;
    
    public function setFacebookUser( FacebookUser $facebookUser ) {
        $this->facebookUser = $facebookUser;
    }
    
    public function getFacebookUser() {
        return $this->facebookUser;
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
    
    /** 
     * @ORM\Column(type="boolean",name="authority",nullable=true) 
     */
    protected $isAuthority = null;
    
    public function getId() {
        return $this->id;
    }
    
    public function setId( $id ) {
        $this->id = $id;
    }
    
    /**
     * @ORM\OneToMany(targetEntity="NeighborhoodPolygonVote",mappedBy="whathoodUser")
     */
    protected $votes;
    
    public function getVotes() {
        return $this->votes;
    }
    
    public function setVotes( ArrayCollection $votes ) {
        $this->votes = $votes;
    }

    
    public function isAuthority() {
        return $this->isAuthority;
    }
    
    public function setAuthority($isAuthority) {
        $this->isAuthority = $isAuthority;
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
