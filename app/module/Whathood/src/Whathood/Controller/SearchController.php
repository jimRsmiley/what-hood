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
class SearchController extends BaseController {

    public function indexAction() {
        $queryString = $this->getUriParameter('q');

        /*
         * get regions
         */
        try {
            $regions = $this->regionMapper()->nameLike( $queryString );
        } catch( \Doctrine\ORM\NoResultException $e ) {
            $regions = array();
        }

        try {
            $neighborhoods = $this->neighborhoodMapper()
                                            ->nameLike( $queryString );
        } catch( \Doctrine\ORM\NoResultException $e ) {
            $neighborhoods = array();
        }

        $this->getLogger()->search( $queryString );

        return new ViewModel( array(
            'queryString' => $queryString,
            'regions' => $regions,
            'neighborhoods' => $neighborhoods
        ));
    }

    public function byPositionAction() {

        $y = $this->params()->fromQuery('y');
        $x = $this->params()->fromQuery('x');

        $format = $this->getUriParameter('format');

        if( empty($x) || empty($y) )
            throw new \InvalidArgumentException("x and y are required");

        $whathoodResult = $this->getWhathoodResult($x,$y);

        return new JsonModel( array (
            'whathood_result' => $whathoodResult->toArray()
        ));
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

    public function getWhathoodResult($x,$y) {
        $neighborhoods = $this->userPolygonMapper()->getByXY($x,$y);
        $whathoodResult = new WhathoodResult();
        $whathoodResult->setLatLng($x,$y);
        $whathoodResult->setNeighborhoods($neighborhoods);
        return $whathoodResult;
    }
}
