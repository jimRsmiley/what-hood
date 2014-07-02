<?php

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

/**
 * Description of WhathoodController
 *
 * @author Jim Smiley twitter:@jimRsmiley
 */
class BaseController extends AbstractActionController {
    
    protected $neighborhoodMapper;
    protected $neighborhoodPolygonMapper;
    protected $whathoodUserMapper;
    protected $regionMapper;
    protected $neighborhoodPolygonVoteMapper;
    
    public function neighborhoodMapper() {
        
        if( $this->neighborhoodMapper == null ) {
            $this->neighborhoodMapper = $this->getServiceLocator()
                    ->get( 'Application\Mapper\NeighborhoodMapper' );
        }
        return $this->neighborhoodMapper;
    }
    
    public function neighborhoodPolygonMapper() {
        
        if( $this->neighborhoodPolygonMapper == null ) {
            $this->neighborhoodPolygonMapper = $this->getServiceLocator()
                    ->get( 'Application\Mapper\NeighborhoodPolygonMapper' );
        }
        return $this->neighborhoodPolygonMapper;
    }
    
    public function neighborhoodPolygonVoteMapper() {
        
        if( $this->neighborhoodPolygonVoteMapper == null ) {
            $this->neighborhoodPolygonVoteMapper = $this->getServiceLocator()
                    ->get( 'Application\Mapper\NeighborhoodPolygonVoteMapper' );
        }
        return $this->neighborhoodPolygonVoteMapper;
    }
    
    public function regionMapper() {
        
        if( $this->regionMapper == null ) {
            $this->regionMapper = $this->getServiceLocator()
                    ->get( 'Application\Mapper\RegionMapper' );
        }
        return $this->regionMapper;
    }
    
    public function whathoodUserMapper() {
        
        if( $this->whathoodUserMapper == null ) {
            $this->whathoodUserMapper = $this->getServiceLocator()
                ->get('Application\Mapper\WhathoodUserMapper');
        }
        return $this->whathoodUserMapper;
    }
    
    public function getAuthenticationService() {
        return $this->getServiceLocator()
                    ->get( 'Application\Model\AuthenticationService' );
    }
    
    public function getUriParameter($key) {
        
        if( $this->params()->fromQuery($key) != null )
            return $this->params()->fromQuery($key);
        
        if( $this->getEvent()->getRouteMatch()->getParam($key) != null ) {
            return $this->getEvent()->getRouteMatch()->getParam($key);
        }
        
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
            'currentWhathoodUser'   => $this->getAuthenticationService()->getWhathoodUser()
        );
        
       $newParams = array_merge($breadCrumbParams,$params);
       return new ViewModel( $newParams );
    }
    
    /**
     * I want an easy way to make the logged in user available
     */
    public function getLoggedInWhathoodUser() {
        return $this->getAuthenticationService()->getWhathoodUser();
    }
    
    public function getLogger() {
        return $this->getServiceLocator()->get('mylogger');
    }
}

?>
