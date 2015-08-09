<?php

namespace Whathood\Controller\Console;

use Whathood\Controller\BaseController;
use Whathood\Entity\NeighborhoodPolygon;
use Whathood\Timer;

class WatcherController extends BaseController
{

    protected $_target_precision;

    protected $_grid_resolution;

    public function watchAction() {
        $api_timer = Timer::start('api');

        $force                  = $this->getRequest()->getParam('force',false);
        $forever                = $this->getRequest()->getParam('forever',false);
        $neighborhood_name      = $this->getRequest()->getParam('neighborhood',null);
        $region_name            = $this->getRequest()->getParam('region',null);
        $this->setGridResolution(
            $this->getRequest()->getParam(
                'grid-res',$this->getDefaultGridResolution()
            ));

        $this->logger()->info("Whathood watcher has started");

        $neighborhood_name = str_replace('+',' ',$neighborhood_name);
        do {
            if ($neighborhood_name and $region_name ) {
                $neighborhood = $this->m()->neighborhoodMapper()
                    ->byName($neighborhood_name,$region_name);
                $user_polygons = $this->m()->userPolygonMapper()
                    ->byNeighborhood($neighborhood);
            }
            else if ($force) {
                $user_polygons = $this->m()->userPolygonMapper()->fetchAllToBuild($force=true);
            }
            else {
                $up_t = Timer::start('gather_user_polygons');
                $user_polygons = $this->m()->userPolygonMapper()
                    ->getUserPolygonsNotAssociatedWithNeighborhoodPolygons();
                $up_t->stop();
            }

            if (!empty($user_polygons)) {
                foreach($user_polygons as $up) {
                    $this->logger()->info(
                        sprintf("\tprocessing new user generated polygon(%s) for neighborhood %s",
                            $up->getId(),
                            $up->getNeighborhood()->getName()
                        )
                    );
                }

                $neighborhoods = $this->collate_neighborhoods($user_polygons);
                foreach($neighborhoods as $n) {
                    $ups = $n->getUserPolygons();

                    try {
                        /* build the border */
                        $timer = Timer::start('generate_border');
                        $electionCollection = $this->m()->electionMapper()->getCollection(
                            $ups,
                            $n->getId(),
                            $this->getGridResolution(),
                            $this->getTargetPrecision()
                        );
                        $this->buildAndSaveNeighborhoodPolygon($electionCollection);
                        $this->buildAndSaveHeatmapPoints($electionCollection);
                    }
                    catch(\Exception $e) {
                        $this->logger()->err($e->getMessage());
                        $this->logger()->err($e->getTraceAsString());
                        $err_msg = "FATAL: the watcher script died because of an error\n";
                        $this->logger()->err($err_msg);
                        die($err_msg);
                    }
                } // foreach neighborhood
            } // if there are user polygons

            if ($forever)
                sleep(5);
        }
        while ($forever);
        $this->logger()->info("watcher finished");
    }

    public function buildAndSaveNeighborhoodPolygon(ElectionCollection $electionCollection) {
        $polygon = $this->m()->electionMapper()->generateBorderPolygon(
            $electionCollection, $n
        );

        $timer->stop();

        if (!$polygon) {
            $this->logger()->warn("Could not construct a neighborhood border for ".$n->getName());
            continue;
        }

        $neighborhoodPolygon = NeighborhoodPolygon::build( array(
            'geom' => $polygon,
            'neighborhood' => $n,
            'user_polygons' => $ups,
            'grid_resolution' => $this->getGridResolution(),
            'target_precision' => $this->getTargetPrecision()
        ));
        $this->logger()->info(
            sprintf("\tid=%s name=%s num_user_polygons=%s build_time=%s mins",
                $n->getId(),
                $n->getName(),
                count($ups),
                $timer->elapsed_minutes()
                )
        );
        $this->m()->neighborhoodPolygonMapper()->save($neighborhoodPolygon);
        return $neighborhoodPolygon;
    }

    public function buildAndSaveHeatmapPoints(ElectionCollection $electionCollection) {
        $heatmap_points = $electionCollection->heatMapPointsByNeighborhood($n);
        $this->m()->heatMapPoint()->deleteByNeighborhood($n);
        $this->m()->heatMapPoint()->savePoints($heatmap_points);
        $this->logger()->info("\tsaved ".count($heatmap_points)." heatmap points");
        return $heatmap_points;
    }

    public function getGridResolution() {
        return $this->_grid_resolution;
    }

    public function setGridResolution($grid_resolution) {
        $this->_grid_resolution = $grid_resolution;
    }

    public function getTargetPercentage() {
        return $this->_target_precision;
    }

    public function setTargetPercentage($target_percentage) {
        $this->_target_precision = $target_percentage;
    }

    public function getDefaultGridResolution() {
        $config = $this->getServiceLocator()->get('Whathood\YamlConfig');
        if (!array_key_exists('default_grid_resolution',$config))
            throw new \Exception('default_grid_resolution not found in yaml config file');
        return $config['default_grid_resolution'];
    }

    public function getDefaultTargetPrecision() {
        $config = $this->getServiceLocator()->get('Whathood\YamlConfig');
        if (!array_key_exists('default_target_precision',$config))
            throw new \Exception('default_target_precision not found in yaml config file');
        return $config['default_target_precision'];
    }

    public function getTargetPrecision() {
        return $this->_target_precision;
    }

    public function setTargetPrecision($target_precision) {
        $this->_target_precision = $target_precision;
    }

    /**
     * we can have multiple user polygons with the same neighborhood, so just return one neighborhood
     */
    public function collate_neighborhoods(array $user_polygons) {
        $neighborhoood_array = array();

        foreach($user_polygons as $up) {
            $neighborhood_array[$up->getNeighborhood()->getId()] = $up->getNeighborhood();
        }

        return array_values($neighborhood_array);
    }

}
