<?php
namespace Whathood\Controller;

use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use Whathood\Spatial\PHP\Types\Geometry\FeatureCollection;
use Whathood\Spatial\PHP\Types\Geometry\Feature;

/**
 * Description of RegionController
 *
 * @author Jim Smiley twitter:@jimRsmiley
 */
class RegionController extends BaseController {
    
    public function showAction() {
        $regionName = $this->getUriParameter('region_name');
        $setNumber = $this->getUriParameter('set_number');
        
        if( empty( $regionName ) ) {
            $regionName = 'Philadelphia';
        }
        
        if( empty( $setNumber ) )
            $setNumber = $this->neighborhoodPolygonMapper ()->getLastCreateEventId();
        
        try {
            $region = $this->regionMapper()->getRegionByName( $regionName );
        } catch( \Doctrine\ORM\NoResultException $e ) {
            $viewModel = new ViewModel( array( 'regionName' => $regionName ) );
            $viewModel->setTemplate('whathood/region/no-region-by-name.phtml');
            return $viewModel;
        }   
        
        $viewModel = $this->getViewModel( array( 
            'region'    => $region,
            'setNumber' => $setNumber
            )
        );
        $viewModel->setTemplate('whathood/region/region-show.phtml');
        return $viewModel;
    }
    
    public function listRegionsAction() {
        
        $mapper = $this->getServiceLocator()
                ->get('Whathood\Mapper\RegionMapper');

        $regions = $mapper->fetchAll();
       
        return new ViewModel( array( 'regions' => $regions ) );
    }
    
    public function editAction() {
        $regionName = $this->getEvent()->getRouteMatch()->getParam('region');
        
        $neighborhoods = $this->neighborhoodMapper()
                                ->getAuthoritativeNeghborhoodsByRegionName($regionName);

        \Zend\Debug\Debug::dump($neighborhoods);
        exit;
        $userName = $neighborhoods[0]->getUser()->getName();
        
        $viewModel = new ViewModel(
                array( 
                    'neighborhoods' => $neighborhoods,
                    'regionName'    => $regionName,
                    'userName'      => $userName
                ) 
            );
        $viewModel->setTemplate('whathood/region/region-show.phtml');
        return $viewModel;
    }
}

?>
