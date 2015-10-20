<?php

namespace Whathood\Controller\Restful;

use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\View\Model\JsonModel;

/**
 *
 * serve neighborhood polygon REST data
 *
 */
class NeighborhoodPolygonRestfulController extends BaseController {

    public function get($id) {
        die("not yet implemented");
    }

    /**
     *
     * latest_polygon_id - get the latest neighborhood polygon based on id
     *
     */
    public function getListAction() {
        $query_type = $this->params()->fromQuery('query_type');

        $neighborhood_id = $this->params()->fromRoute('neighborhood_id');

        if (empty($neighborhood_id))
            return $this->badRequestJson("neighborhood_id must be defined");

        // get the neighborhood
        $neighborhood = $this->m()->neighborhoodMapper()->byId($neighborhood_id);

        try {
            // get the latest NeighborhoodPolygon
            $ret_array = $this->m()->neighborhoodPolygonMapper()
                ->latestByNeighborhood($neighborhood)->toArray();
        }
        catch (\Doctrine\ORM\NoResultException $e) {
            $ret_array = null;
        }

        return new JsonModel( $ret_array );
    }

    public function byRegionAction() {
        $regionName = $this->getUriParameter('region');

        try {
            $region = $this->m()->regionMapper()->getRegionByName( $regionName );
            $json = $this->m()->neighborhoodPolygonMapper()
                    ->getNeighborhoodPolygonsAsGeoJsonByRegion( $region );

            $array = \Zend\Json\Json::decode( $json, \Zend\Json\Json::TYPE_ARRAY );
        }
        catch(\Exception $e) {
            $this->logger()->err($e);
            $this->getResponse()->setStatusCode(400);
            $array = array( 'message' => 'unable to complete request' );
        }
        return new JsonModel( $array );
    }
}
