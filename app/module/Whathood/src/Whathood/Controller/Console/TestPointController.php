<?php
namespace Whathood\Controller\Console;

use Whathood\Spatial\PHP\Types\Geometry\MultiPoint;
use Whathood\Controller\BaseController;

/**
 * Handle test point actions from the console
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

        if (empty($neighborhood_name) or empty($region_name) or empty($grid_resolution))
            die("neighborhood_name,region_name and grid_res must all be defined");
        $neighborhood = $this->m()->neighborhoodMapper()->byName($neighborhood_name,$region_name);

        if (empty($neighborhood))
            die("no neighborhood found");
        $user_polygons = $this->m()->userPolygonMapper()->byNeighborhood($neighborhood);

        if (empty($user_polygons))
            die("no user polygons found for neighborhood");

        $timer = \Whathood\Timer::init();
        $points = $this->m()->testPointMapper()->createByUserPolygons($user_polygons,$grid_resolution);
        $test_point_ms = $timer->elapsed_milliseconds();
        $test_point_count = count($points);

        $this->logger()->info(
            sprintf("generated %s test points in %sms; %sms per 1000 points",
                $test_point_count,
                $test_point_ms,
                round($test_point_ms/$test_point_count*1000,1)
            )
        );

        if (empty($points))
            die("no points returned with grid_resolution $grid_resolution");

        $timer = \Whathood\Timer::init();
        $consensus_col = $this->m()->electionMapper()->buildElectionPointCollection($points);
        $consensus_seconds = $timer->elapsed_seconds();
        $this->logger()->info(
            sprintf("got consensus in %s seconds; %sms per point",
                $consensus_seconds,
                round($consensus_seconds/count($points)*1000,2)
            )
        );
    }
}
