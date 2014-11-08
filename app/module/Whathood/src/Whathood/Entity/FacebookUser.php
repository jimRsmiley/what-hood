<?php
namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;
/**
 * @ORM\Entity
 * @ORM\Table(name="facebook_user")
 */
class FacebookUser extends \ArrayObject {
   
    public function __construct($array=null) {
        if( !empty($array) ) {
            $hydrator = new \Zend\Stdlib\Hydrator\ClassMethods();
            $hydrator->hydrate( $array, $this );
        }
    }
    
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     */
    protected $id = null;
    
    public function getId() {
        return $this->id;
    }
    
    public function setId( $id ) {
        $this->id = $id;
    }
    
    /** 
     * @ORM\Column(type="string",nullable=false) 
     */
    protected $name = null;

    public function getName() {
        return $this->name;
    }
    
    public function setName( $name ) {
        $this->name = $name;
    }
    
    /**
     * @ORM\Column(name="first_name",type="string",nullable=false)
     */
    protected $firstName = null;
    
    public function getFirstName() {
        return $this->firstName;
    }
    
    public function setFirstName( $firstName ) {
        $this->firstName = $firstName;
    }
    
    /**
     * @ORM\Column(name="last_name",type="string",nullable=false)
     */
    protected $lastName = null;
    
    public function getLastName() {
        return $this->firstName;
    }
    
    public function setLastName( $lastName ) {
        $this->lastName = $lastName;
    }
    
    /**
     * @ORM\Column(name="link",type="string",nullable=false)
     */
    protected $link = null;
    
    public function getLink() {
        return $this->link;
    }
    
    public function setLink( $link ) {
        $this->link = $link;
    }

        /**
     * @ORM\Column(name="username",type="string",nullable=false)
     */
    protected $username = null;
    
    public function getUsername() {
        return $this->username;
    }
    
    public function setUsername( $username ) {
        $this->username = $username;
    }
    
    /**
     * @ORM\Column(name="gender",type="string",nullable=false)
     */
    protected $gender = null;
    
    public function getGender() {
        return $this->gender;
    }
    
    public function setGender( $gender ) {
        $this->gender = $gender;
    }
    
    /**
     * @ORM\Column(name="timezone",type="string",nullable=false)
     */
    protected $timezone = null;
    
    public function getTimezone() {
        return $this->timezone;
    }
    
    public function setTimezone( $timezone ) {
        $this->timezone = $timezone;
    }
    
    /**
     * @ORM\Column(name="locale",type="string",nullable=false)
     */
    protected $locale = null;
    
    public function getLocale() {
        return $this->locale;
    }
    
    public function setLocale( $locale ) {
        $this->locale = $locale;
    }
    
    /**
     * @ORM\Column(name="verified",type="string",nullable=false)
     */
    protected $verified = null;
    
    public function getVerified() {
        return $this->verified;
    }
    
    public function setVerified( $verified ) {
        $this->verified = $verified;
    }
    
    /**
     * @ORM\Column(name="updated_time",type="string",nullable=false)
     */
    protected $updatedTime = null;
    
    public function getUpdatedTime() {
        return $this->updatedTime;
    }
    
    public function setUpdatedTime( $updatedTime ) {
        $this->updatedTime = $updatedTime;
    }
    
    public function getLastInitial() {
        
        if( $this->lastName != null )
            return substr( $this->lastName, 0, 1 );
        else
            return null;
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
