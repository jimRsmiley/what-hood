<?php
namespace Whathood\Controller\Restful;

use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use Zend\Mvc\Controller\AbstractRestfulController;

use Whathood\Spatial\PHP\Types\Geometry\FeatureCollection;
use Whathood\Spatial\PHP\Types\Geometry\Feature;

/**
 * Description of RegionController
 *
 * @author Jim Smiley twitter:@jimRsmiley
 */
class RegionRestController extends AbstractRestfulController {

    public function getList() {
        $regions = $this->getRegionMapper()->fetchAll();


        $array = [];
        foreach( $regions as $region ) {
            array_push( $array, $region->toArray() );
        }
        return new JsonModel( array( "regions" => $array ) );
    }

    public function get($id) {
        $region = $this->getRegionMapper()->byId($id);

        return new JsonModel( array( "region" => $region->toArray()  ) );
    }

    public function create($data) {

    }

    public function updateAction() {

    }

    public function update($id,$data) {

    }

    public function delete($id) {

    }

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

    public function getRegionMapper() {
        return $this->getServiceLocator()->get('Whathood\Mapper\RegionMapper');
    }
}

?>
