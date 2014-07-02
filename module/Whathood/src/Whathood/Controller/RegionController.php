<?php
namespace Application\Controller;

use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use Application\Spatial\PHP\Types\Geometry\FeatureCollection;
use Application\Spatial\PHP\Types\Geometry\Feature;

/**
 * Description of RegionController
 *
 * @author Jim Smiley twitter:@jimRsmiley
 */
class RegionController extends BaseController {
    
    public function showAction() {
        
        $regionName = $this->getUriParameter('region_name');
        
        if( empty( $regionName ) ) {
            throw new \InvalidArgumentException('regionName may not be null');
        }
        
        try {
            $region = $this->regionMapper()->byName( $regionName );
        } catch( \Doctrine\ORM\NoResultException $e ) {
            $viewModel = new ViewModel( array( 'regionName' => $regionName ) );
            $viewModel->setTemplate('application/region/no-region-by-name.phtml');
            return $viewModel;
        }   
        
        $featureCollection = new FeatureCollection();
        $featureCollection->addGeometry(
                                new Feature( $region->getBorderPolygon() ) );
        
        $viewModel = $this->getViewModel( array( 
                    'region'    => $region,
                    'featureCollection' => $featureCollection
            )
        );
        $viewModel->setTemplate('application/region/region-show.phtml');
        return $viewModel;
    }
    
    public function borderAction() {
        $regionName = $this->getUriParameter('region_name');
        
        if( empty( $regionName ) ) {
            throw new \InvalidArgumentException('regionName may not be null');
        }
        
        $region = $this->regionMapper()->byName( $regionName );
        
        $featureCollection = new FeatureCollection();
        $featureCollection->addGeometry(
                                new Feature( $region->getBorderPolygon() ) );
        
        return new JsonModel( $featureCollection->toGeoJsonArray() );
    }
    
    public function listRegionsAction() {
        
        $mapper = $this->getServiceLocator()
                ->get('Application\Mapper\RegionMapper');

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
        $viewModel->setTemplate('application/region/region-show.phtml');
        return $viewModel;
    }
}

?>
