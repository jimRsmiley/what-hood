<?php
namespace Whathood\Controller;

use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use Whathood\Model\Whathood\WhathoodResult;
/**
 * Description of SearchController
 *
 * @author Jim Smiley twitter:@jimRsmiley
 */
class WhathoodController extends BaseController {
    
    public function byPositionAction() {
        
        $lat = $this->params()->fromQuery('lat');
        $lng = $this->params()->fromQuery('lng');

        $format = $this->getUriParameter('format');
        
        if( empty($lat) || empty($lng) )
            throw new \InvalidArgumentException("you need to give me lat/lng");
        
        
        
        $whathoodResult = $this->getPolygon( $lat, $lng );
        
        $responseModel = null;
        if( $format == 'json' ) {
            return new JsonModel( array (
                'whathood_result' => $whathoodResult->toArray()
            ));
        } else {
            $viewModel = new ViewModel( 
                array( 'whathoodResult' => $whathoodResult,
                    'regionName' => $whathoodResult->getRegionName(),
                    'currentLocation' => true ) );
            $viewModel->setTemplate('application/whathood/result.phtml');
            return $viewModel;
        }
    }
    
    public function byAddressAction() {
        $address = $this->getUriParameter('address');
        $regionName = $this->getUriParameter('region_name');
        $format = $this->getUriParameter('format');
        
        if( empty($address) )
            throw new \InvalidArgumentException("address may not be null");
        
        $adapter  = new \Geocoder\HttpAdapter\BuzzHttpAdapter();
        $geocoder = new \Geocoder\Geocoder();
        $geocoder->registerProviders(array(
            new \Geocoder\Provider\GoogleMapsProvider(
                $adapter, $locale = null, $thisRegion = null, $useSsl = false
            ),
        )); 
        
        $fullAddress=$address.','.$regionName;
        //die( \Zend\Debug\Debug::dump($address) );
        $result = $geocoder->geocode($fullAddress);
        
        $whathoodResult = $this->getPolygon($result->getLatitude(),
                $result->getLongitude() );
        
        $this->getLogger()->addressRequest( $regionName, $address );
        
        if( $format == 'json' ) {
            return new JsonModel( array (
                'whathood_result' => $whathoodResult->toArray()
            ));
        } else {
            $viewModel = $this->getViewModel( array( 
                'whathoodResult' => $whathoodResult,
                'regionName' => $result->getCity(),
                'address'   => $address ) 
            );
            $viewModel->setTemplate('application/whathood/result.phtml');
            return $viewModel;
        }
    }
    
    public function getPolygon( $lat, $lng ) {
        $userPolygonMapper = $this->userPolygonMapper();
        $neighborhoods = $userPolygonMapper->getNeighborhoodPolygonsByLatLng($lng,$lat);
        
        $whathoodResult = new WhathoodResult();
        $whathoodResult->setLatLng( $lat, $lng );
        $whathoodResult->setNeighborhoods( $neighborhoods );
        return $whathoodResult;
    }
}

?>
