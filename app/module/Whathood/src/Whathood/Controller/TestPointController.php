<?php
namespace Whathood\Controller;

use Whathood\Spatial\PHP\Types\Geometry\MultiPoint;
/**
 * Handle test point actions
 *
 */
class TestPointController extends BaseController
{

    /**
     * return geojson representation of test points given either
     *
     *  - neighborhood name and region name
     */
    public function showAction() {
        $neighborhood_name = $this->paramfromRoute('neighborhood');
        $region_name       = $this->params()->fromRoute('region');
        $grid_resolution   = $this->params()->fromRoute('grid-resolution');

        $neighborhood = $this->neighborhoodMapper()->byName($neighborhood_name,$region_name);

        $user_polygons = $this->userPolygonMapper()->byNeighborhood($neighborhood);

        $geojson = $this->geojsonByUserPolygons($user_polygons,$grid_resolution);

        return $geojson;
    }

    /**
     * given an array of user_polygons, and a grid resolution, return the grid of test points
     * that covers them
     *
     * @return string geojson string of test points
     */
    public function geojsonByUserPolygons($user_polygons,$grid_resolution) {
        $points = $this->testPointMapper()->createByUserPolygons($user_polygons,$grid_resolution);

        $multi_point = new MultiPoint($points);
        $geojson = $this->concaveHullMapper()->toPolygon($multi_point,$grid_resolution);
    }
}
