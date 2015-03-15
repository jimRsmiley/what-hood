<?php

namespace Whathood\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Whathood\Entity\WhathoodUser;

/**
 * Description of WhathoodController
 *
 * @author Jim Smiley twitter:@jimRsmiley
 */
class BaseController extends AbstractActionController {

    private $neighborhoodMapper;
    private $userPolygonMapper;
    private $neighborhoodPolygonMapper;
    private $whathoodUserMapper;
    private $regionMapper;
    private $neighborhoodStrengthOfIdentityMapper;
    private $testPointMapper;
    private $contentiousPointMapper;
    private $_concaveHullMapper;

    public function onDispatch(\Zend\Mvc\MvcEvent $event) {
        $this->timer = \Whathood\Timer::init();

        return parent::onDispatch($event);
    }

    public function concaveHullMapper() {

        if( $this->_concaveHullMapper == null ) {
            $this->_concaveHullMapper = $this->getServiceLocator()
                    ->get( 'Whathood\Mapper\ConcaveHullMapper' );
        }
        return $this->_concaveHullMapper;
    }


    public function neighborhoodMapper() {

        if( $this->neighborhoodMapper == null ) {
            $this->neighborhoodMapper = $this->getServiceLocator()
                    ->get( 'Whathood\Mapper\NeighborhoodMapper' );
        }
        return $this->neighborhoodMapper;
    }

    public function userPolygonMapper() {

        if( $this->userPolygonMapper == null ) {
            $this->userPolygonMapper = $this->getServiceLocator()
                    ->get( 'Whathood\Mapper\UserPolygonMapper' );
        }
        return $this->userPolygonMapper;
    }

    public function testPointMapper() {

        if( $this->testPointMapper == null ) {
            $this->testPointMapper = $this->getServiceLocator()
                    ->get( 'Whathood\Mapper\TestPointMapper' );
        }
        return $this->testPointMapper;
    }

    public function neighborhoodPolygonMapper() {

        if( $this->neighborhoodPolygonMapper == null ) {
            $this->neighborhoodPolygonMapper = $this->getServiceLocator()
                    ->get( 'Whathood\Mapper\NeighborhoodPolygonMapper' );
        }
        return $this->neighborhoodPolygonMapper;
    }

    public function regionMapper() {

        if( $this->regionMapper == null ) {
            $this->regionMapper = $this->getServiceLocator()
                    ->get( 'Whathood\Mapper\RegionMapper' );
        }
        return $this->regionMapper;
    }

    public function whathoodUserMapper() {

        if( $this->whathoodUserMapper == null ) {
            $this->whathoodUserMapper = $this->getServiceLocator()
                ->get('Whathood\Mapper\WhathoodUserMapper');
        }
        return $this->whathoodUserMapper;
    }

    public function neighborhoodStrengthOfIdentityMapper() {
        if( $this->neighborhoodStrengthOfIdentityMapper == null ) {
            $this->neighborhoodStrengthOfIdentityMapper = $this->getServiceLocator()
                                    ->get('Whathood\Mapper\NeighborhoodPointStrengthOfIdentityMapper');
        }
        return $this->neighborhoodStrengthOfIdentityMapper;
    }

    public function contentiousPointMapper() {

        if( $this->contentiousPointMapper == null ) {
            $this->contentiousPointMapper = $this->getServiceLocator()
                ->get('Whathood\Mapper\ContentiousPointMapper');
        }
        return $this->contentiousPointMapper;
    }

    public function createEventMapper() {

        if( $this->contentiousPointMapper == null ) {
            $this->contentiousPointMapper = $this->getServiceLocator()
                ->get('Whathood\Mapper\CreateEventMapper');
        }
        return $this->contentiousPointMapper;
    }

    // a more accurate function description
    public function getRequestParameter($key) {
        $val = $this->getUriParameter($key);
        return str_replace('+',' ',$val);
    }

    /**
     *  return the route parameter replacing any pluses,
     *  useful for console route params which can't have white space values
     */
    public function paramFromRoute($key) {
        $val = $this->params()->fromRoute($key);
        return str_replace('+',' ',$val);
    }

    /**
     * in order of trying:
     *      query
     *      route
     */
    public function getUriParameter($key) {
        if( $this->params()->fromQuery($key) != null )
            return $this->params()->fromQuery($key);
        if( $this->getEvent()->getRouteMatch()->getParam($key) != null )
            return $this->getEvent()->getRouteMatch()->getParam($key);
        return false;
    }

    /**
     * our view scripts need breadcrumbs quite regularly so let's use this model
     * to create our view model with the selected params and add on region, user
     * and neighborhood names so we don't have to keep doing it.
     *
     * @param type $params
     */
    public function getViewModel( $params ) {

        $breadCrumbParams = array(
            'regionName'    => $this->getUriParameter('region_name'),
            'neighborhoodName' => $this->getUriParameter('neighborhood_name'),
        );

       $newParams = array_merge($breadCrumbParams,$params);
       return new ViewModel( $newParams );
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

    protected function _getLogger() {
        return $this->getServiceLocator()->get('Whathood\Logger');
    }

    protected function _getConsoleLogger() {
        return $this->getServiceLocator()->get('Whathood\ConsoleLogger');
    }

    // sugar
    public function logger() {
        if ( $this->getRequest() instanceof \Zend\Console\Request) {
            return $this->_getConsoleLogger();
        }
        else {
            return $this->_getLogger();
        }
    }

    public static function prompt_user($msg) {
        \Whathood\Util::prompt_user("$msg; Enter to continue; CTRL-C to cancel");
    }

}

?>
