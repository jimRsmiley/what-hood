<?php
namespace Application\View\Helper;

use Zend\ServiceManager\ServiceManager;
/**
 * Helps view scripts with displaying authenticated user information
 *
 * @author Jim Smiley twitter:@jimRsmiley
 */
class Auth extends \Zend\View\Helper\AbstractHelper {
    
    protected  $auth;
    protected  $sm;
    
    public function setServiceLocator( ServiceManager $sm) {
        $this->sm = $sm;
    }
    
    public function getAuthenticationService() {
        
        if( $this->auth == null ) {
            $this->auth = $this->sm->get('Application\Model\AuthenticationService');
        }
        
        return $this->auth;
    }
    
    public function isLoggedIn() {
        if( $this->getAuthenticationService()->hasIdentity() )
            return true;
        else
            return false;
    }
    
    public function getWhathoodUser() {
        return $this->getAuthenticationService()->getWhathoodUser();
    }
    
    public function logoutUrl() {
        $redirectTo =  $this->getServerUri();
        return '/application/auth/logout?redirect_to='.$redirectTo;
    }
    
    public function loginUrl() {
        $redirectTo =  $this->getServerUri();
        return '/application/auth/login?redirect_to='.$redirectTo;
    }
    
    public function getServerUri() {
        $request = $this->sm->get('request');
        
        return $request->getUriString();
        
    }
}

?>
