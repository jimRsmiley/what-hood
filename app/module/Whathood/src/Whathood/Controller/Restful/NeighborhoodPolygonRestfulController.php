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
    public function getList() {
        $query_type = $this->params()->fromQuery('query_type');

        $neighborhood_latest_polygon_id = $this->params()
                                    ->fromQuery('neighborhood_latest_polygon_id');

        if ('latest'==$query_type) {
            $neighborhood_id = $this->params()->fromQuery('neighborhood_id');

            // get the neighborhood
            $neighborhood = $this->m()->neighborhoodMapper()->byId($neighborhood_id);

            // get the latest NeighborhoodPolygon
            $neighborhood_polygon = $this->m()->neighborhoodPolygonMapper()
                ->latestByNeighborhood($neighborhood);

            return new JsonModel( $neighborhood_polygon->toArray() );
        }
        return new JsonModel( array(
            'msg' => 'not yet implemented' ));
    }
}
