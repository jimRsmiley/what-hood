<?php
namespace Whathood\Controller;

use Zend\View\Model\ViewModel;
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
        $neighborhood_name = $this->getRequestParameter('neighborhood_name');
        $region_name       = $this->getRequestParameter('region_name');
        $grid_resolution   = $this->getRequestParameter('grid_res');

        if (empty($neighborhood_name))
            throw new \InvalidArgumentException("neighborhood_name may not be empty");
        $geojson_url = "/api/v1/test-point?neighborhood_name=$neighborhood_name&region_name=$region_name&grid_res=$grid_resolution";
        return new ViewModel( array(
            'geojson_url'   => $geojson_url
        ));
    }
}
