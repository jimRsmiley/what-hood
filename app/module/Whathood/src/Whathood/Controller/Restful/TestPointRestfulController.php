<?php
namespace Whathood\Controller\Restful;

use Whathood\Spatial\PHP\Types\Geometry\MultiPoint;
use Whathood\Controller\Restful\BaseController;
use Zend\View\Model\JsonModel;
/**
 * Handle test point actions
 */
class TestPointRestfulController extends BaseController
{

    /**
     * @api {get} /testpoint
     * @apiName getTestPoints
     * @apiGroup TestPoint
     *
     * @apiParam {String} neighborhoodName the name of the neighborhood
     * @apiParam {String} regionName the name of the region
     *
     * @apiSuccess {String} type the geometry type
     * @apiSuccess {String} coordinates  an array of coordinates
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     *     {
     *       "type": "MultiPoint",
     *       "coordinates": [
     *          [1,2],
     *          [3,4],
     *          .....
     *       ]
     *     }
     */
    public function getList() {
        $neighborhood_name = $this->getRequestParameter('neighborhood_name');
        $region_name       = $this->getRequestParameter('region_name');
        $grid_resolution   = $this->getRequestParameter('grid_res');

        if (empty($neighborhood_name) or empty($region_name) or empty($grid_resolution))
            return $this->badRequestJson("neighborhood_name,region_name and grid_res must all be defined");
        $neighborhood = $this->m()->neighborhoodMapper()->byName($neighborhood_name,$region_name);

        if (empty($neighborhood))
            return $this->badRequestJson("no neighborhood found");
        $user_polygons = $this->m()->userPolygonMapper()->byNeighborhood($neighborhood);

        if (empty($user_polygons))
            return $this->badRequestJson("no user polygons found for neighborhood");

        $points = $this->m()->testPointMapper()->createByUserPolygons($user_polygons,$grid_resolution);

        if (empty($points))
            return $this->badRequestJson("no points returned with grid_resolution $grid_resolution");

        $multi_point = new MultiPoint($points);

        return new JsonModel(
            \Whathood\Spatial\Util::toGeoJsonArray($multi_point)
        );
    }
}
