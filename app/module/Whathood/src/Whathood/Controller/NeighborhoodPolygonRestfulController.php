<?php

namespace Whathood\Controller;

use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\View\Model\JsonModel;

/**
 *
 * serve neighborhood polygon REST data
 *
 */
class NeighborhoodPolygonRestfulController extends BaseRestfulController {

    public function get($id) {
        die("not yet implemented");
    }


    /**
     *
     * latest_polygon_id - get the latest neighborhood polygon based on id
     *
     */
    public function getList() {
        $neighborhood_latest_polygon_id = $this->params()->fromQuery('neighborhood_latest_polygon_id');

        if (!empty($neighborhood_latest_polygon_id)) {
            $n = $this->neighborhoodPolygonMapper()->latestByNeighborhoodId($neighborhood_latest_polygon_id);
            return new JsonModel( $n->toArray() );
        }
        return new JsonModel( array(
            'msg' => 'not yet implemented' ));
    }
}
