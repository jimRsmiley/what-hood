<?php

namespace Whathood\Controller\Restful;

use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\View\Model\JsonModel;
use Whathood\Election\PointElection;
use Whathood\Spatial\PHP\Types\Geometry\MultiPoint;

/**
 *
 * serve neighborhood polygon REST data
 *
 */
class NeighborhoodBoundaryRestfulController extends BaseController {

    public function get($id) {
        die("not yet implemented");
    }

    /**
     * @api {get} /neighborhood-border/debug-build/:region/:neighborhood/:grid_resolution
     *
     * @apiName getBorderDebugBuild
     * @apiGroup NeighborhoodBoundary
     *
     * @apiParam {String} regionName the name of the region
     * @apiParam {String} neighborhoodName the name of the neighborhood
     * @apiParam {String} gridResolution the desired grid resolution
     *
     * @apiSuccess {Object} boundary - the boundary geometry
     * @apiSuccess {Array} neighborhood election points
     */
    public function debugBuildAction() {
        $neighborhood_name  = $this->getRequestParameter('neighborhood');
        $region_name        = $this->getRequestParameter('region');
        $grid_resolution    = $this->getRequestParameter('grid_res');

        if (empty($neighborhood_name))
            throw new \InvalidArgumentException("neighborhood must be defined");
        if (empty($grid_resolution))
            throw new \InvalidArgumentException("grid_res must be defined");

        $neighborhood = $this->m()->neighborhoodMapper()
            ->byName($neighborhood_name, $region_name);
        $userPolygons = $this->m()->userPolygonMapper()
            ->byNeighborhood($neighborhood);
        $pointElectionCollection = $this->m()->pointElectionMapper()->getCollection(
            $userPolygons,
            $neighborhood->getId(),
            $grid_resolution
        );

        $neighborhoodWinningPointElections = $pointElectionCollection->byNeighborhood($neighborhood);
        $boundary_polygon = $this->getServiceLocator()->get('Whathood\Spatial\Neighborhood\Boundary\BoundaryBuilder')
            ->build($pointElectionCollection, $neighborhood);

        $points = $this->m()->testPointMapper()
            ->createByUserPolygons($userPolygons, $grid_resolution);

        $multi_point = new MultiPoint($points);
        return new JsonModel(array(
            'boundary' => $boundary_polygon->toArray(),
            'all_point_elections' => PointElection::allToArrays($pointElectionCollection->getPointElections()),
            'neighborhood_election_points' => PointElection::allToArrays($neighborhoodWinningPointElections),
            'test_points' => \Whathood\Spatial\Util::toGeoJsonArray($multi_point)
        ));
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
            // get the latest NeighborhoodBoundary
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
                    ->getNeighborhoodBoundarysAsGeoJsonByRegion( $region );

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
