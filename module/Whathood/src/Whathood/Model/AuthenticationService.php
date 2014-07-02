<?php

namespace Whathood\Model;

use \Whathood\Entity\WhathoodUser;
/**
 * Description of AuthenticationStorage
 *
 * @author jsmiley
 */
class AuthenticationService  {

    protected $namespace = 'whathood';
    protected $sessionUserStr = 'whathoodUser';
    protected $session = null;
    protected $config = null;
    protected $fb = null;
    protected $userProfile = null;
    
    public function __construct($config) {
        $this->config = $config;
        $this->session = new \Zend\Session\Container($this->namespace);
    }
    
    public function getWhathoodUser() {
        return unserialize(
                $this->session->offsetGet($this->sessionUserStr)
        );
    }
    
    public function setWhathoodUser( WhathoodUser $whathoodUser) {
        $this->session->offsetSet(
                $this->sessionUserStr,
                serialize($whathoodUser)
        );
    }
    
    public function clear() {
        $this->session->offsetUnset($this->sessionUserStr);
    }
    
    public function hasIdentity() {
        return $this->session->offsetExists($this->sessionUserStr);
    }
    
    public function getFacebook() {
        if( $this->fb == null ) {
            $this->fb = new \Facebook(array(
              'appId'  => $this->getFacebookAppId(),
              'secret' => $this->getFacebookSecret()
            ));
        }        
        return $this->fb;
    }
    
    public function setServiceLocator($serviceLocator) {
        $this->serviceLocator = $serviceLocator;
    }
    
    public function getFacebookAppId() {
        return $this->config['facebook']['appId'];
    }
    
    public function getFacebookSecret() {
        return $this->config['facebook']['secret'];
    }
}

?>
