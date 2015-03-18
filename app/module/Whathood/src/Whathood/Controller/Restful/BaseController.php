<?php

namespace Whathood\Controller\Restful;

use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\View\Model\JsonModel;

/**
 *  the base class for restful controllers
 */
class BaseController extends AbstractRestfulController {

    private $_mapper_builder;

    public function m() {
        if ($this->_mapper_builder == null)
            $this->_mapper_builder = $this->getServiceLocator()
                ->get('Whathood\Mapper\Builder');
        return $this->_mapper_builder;
    }

    public function badRequestJson($msg) {
        $this->getResponse()->setStatusCode(400);
        return new JsonModel(array('msg'=>$msg));
    }

    // a more accurate function description
    public function getRequestParameter($key) {
        $val = $this->getUriParameter($key);
        if ($val != null) return str_replace('+',' ',$val);
    }

    /**
     *  return the route parameter replacing any pluses,
     *  useful for console route params which can't have white space values
     */
    public function paramFromRoute($key) {
        $val = $this->params()->fromRoute($key);
        if ($val != null) return str_replace('+',' ',$val);
    }

    /**
     * in order of trying:
     *      query
     *      route
     */
    public function getUriParameter($key) {
        if( $this->params()->fromQuery($key) != null)
            return $this->params()->fromQuery($key);
        if( $this->getEvent()->getRouteMatch()->getParam($key) != null )
            return $this->getEvent()->getRouteMatch()->getParam($key);
        return false;
    }

    public function getUser() {
	    if($this->zfcUserAuthentication()->hasIdentity()) {
		    return $this->zfcUserAuthentication()->getIdentity();
	    }
	    return null;
    }

	public function getUserRoleNames() {
		$user = $this->getUser();
		$role_names = array();
		if( $user) {
			foreach($user->getRoles() as $role) {
				array_push($role_names, $role->getRoleId());
			}
		}
		return $role_names;
	}

    public function getWhathoodUser() {
        if ( 'test' == getenv('APPLICATION_ENV')) {
            $ip_address = "127.0.0.1";
        }
        else {
            $ip_address = $this->getRequest()->getServer()->get('REMOTE_ADDR');
        }
        return new WhathoodUser( array(
            'ip_address' => $ip_address ));
    }

    public function getLogger() {
        return $this->getServiceLocator()->get('Whathood\Logger');
    }

    public function getConsoleLogger() {
        return $this->getServiceLocator()->get('Whathood\ConsoleLogger');
    }

    // sugar
    public function logger() {
        if ( $this->getRequest() instanceof \Zend\Console\Request) {
            return $this->getConsoleLogger();
        }
        else {
            return $this->getLogger();
        }
    }
}

?>
