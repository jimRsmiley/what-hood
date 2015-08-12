<?php

namespace Whathood\Controller;

use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use Whathood\Spatial\PHP\Types\Geometry\FeatureCollection;
use Whathood\Spatial\PHP\Types\Geometry\Feature;

class RegionController extends BaseController {

    /**
    *   will default to the last create event if one isn't supplied in the get
    *   parameter set_number
    */
    public function showAction() {
        $regionName = $this->getUriParameter('region');

        /**
        *   let's default to Philadelphia when not supplied
        */
        if (empty($regionName)) {
            $regionName = 'Philadelphia';
        }

        try {
            $region = $this->m()->regionMapper()->getRegionByName( $regionName );
        } catch( \Doctrine\ORM\NoResultException $e ) {
            $viewModel = new ViewModel( array( 'regionName' => $regionName ) );
            $viewModel->setTemplate('whathood/region/no-region-by-name.phtml');
            return $viewModel;
        }

        $viewModel = $this->getViewModel( array(
            'region'    => $region,
            )
        );
        return $viewModel;
    }

    public function listRegionsAction() {
        $mapper = $this->m()->regionMapper();
        $regions = $mapper->fetchAll();
        return new ViewModel( array( 'regions' => $regions ) );
    }

    public function editAction() {
        $regionName = $this->getEvent()->getRouteMatch()->getParam('region');

        $neighborhoods = $this->m()->neighborhoodMapper()
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
