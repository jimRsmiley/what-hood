<?php
namespace Whathood\Controller;

/**
 * Handle test point actions
 *
 */
class TestPointController extends BaseController
{

    public function showAction() {
        $neighborhood_name = $this->paramfromRoute('neighborhood');
        $region_name       = $this->params()->fromRoute('region');
        $grid_resolution   = $this->params()->fromRoute('grid-resolution');

        $neighborhood = $this->neighborhoodMapper()->byName($neighborhood_name,$region_name);

        $user_polygons = $this->userPolygonMapper()->byNeighborhood($neighborhood);

        $geojson = $this->geojsonByUserPolygons($user_polygons,$grid_resolution);

        return $geojson;
    }

    public function geojsonByUserPolygons($user_polygons,$grid_resolution) {
        return $this->testPointMapper()->createByUserPolygons($user_polygons,$grid_resolution);
    }
}
