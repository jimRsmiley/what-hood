<?php

namespace Whathood\Controller\Console;

use Whathood\Controller\BaseController;
use Whathood\Entity\NeighborhoodPolygon;
use Whathood\Timer;
use Whathood\Election\PointElectionCollection;
use Whathood\Entity\Neighborhood;

class WatcherController extends BaseController
{

    protected $_target_precision;

    protected $_grid_resolution;

    public function watchAction() {
        $this->whathoodConfig();
        $api_timer = Timer::start('api');

        $force                  = $this->getRequest()->getParam('force',false);
        $forever                = $this->getRequest()->getParam('forever',false);
        $neighborhood_name      = $this->getRequest()->getParam('neighborhood',null);
        $region_name            = $this->getRequest()->getParam('region',null);

        $this->logger()->info("Whathood watcher has started");
        $this->logger()->info("grid-resolution: ".rtrim(sprintf("%.8F",$this->getGridResolution()),"0"));

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
                $this->logFoundUserBorders($user_polygons);

                foreach($this->collate_neighborhoods($user_polygons) as $n) {
                    $ups = $n->getUserPolygons();

                    $this->logger()->info(
                        sprintf("\tprocessing id=%s name=%s num_user_polygons=%s",
                            $n->getId(),
                            $n->getName(),
                            count($ups)
                            )
                    );
                    try {
                        /* build the border */
                        $timer = Timer::start('generate_border');
                        $electionCollection = $this->m()->electionMapper()->getCollection(
                            $ups,
                            $n->getId(),
                            $this->getGridResolution(),
                            $this->getTargetPrecision()
                        );

                        if (empty($electionCollection->getPoints())) {
                           $this->logger()->warn("electionCollection contains no points");
                        }
                        else {
                            $this->buildAndSaveNeighborhoodPolygon($electionCollection, $n, $ups);
                            $this->logger()->info("\t\tsaved neighborhood polygon");
                            $this->buildAndSaveHeatmapPoints($electionCollection, $n);
                        }
                        $timer->stop();
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

    public function logFoundUserBorders($user_polygons) {
        foreach($user_polygons as $up) {
            $this->logger()->info(
                sprintf("\tfound new user generated polygon(%s) for neighborhood %s",
                    $up->getId(),
                    $up->getNeighborhood()->getName()
                )
            );
        }
    }

    public function buildAndSaveNeighborhoodPolygon(PointElectionCollection $electionCollection, Neighborhood $n,$ups) {
        $polygon = $this->m()->electionMapper()->generateBorderPolygon(
            $electionCollection, $n
        );

        if (!$polygon) {
            $this->logger()->warn("Could not construct a neighborhood border for ".$n->getName());
            continue;
        }

        $neighborhoodPolygon = NeighborhoodPolygon::build( array(
            'geom' => $polygon,
            'neighborhood' => $n,
            'user_polygons' => $ups,
            'grid_resolution' => $this->getGridResolution()
        ));
        $this->m()->neighborhoodPolygonMapper()->save($neighborhoodPolygon);
        return $neighborhoodPolygon;
    }

    public function buildAndSaveHeatmapPoints(PointElectionCollection $electionCollection, Neighborhood $n) {
        $heatmap_points = $electionCollection->heatMapPointsByNeighborhood($n);

        if (!empty($heatmap_points)) {
            $this->m()->heatMapPoint()->deleteByNeighborhood($n);
            $this->m()->heatMapPoint()->savePoints($heatmap_points);
            $this->logger()->warn("\t\tsaved ".count($heatmap_points)." heatmap points from " . count($electionCollection->getPoints()) . " points");
        }
        else
            $this->logger()->info("\t\tno heatmap_points generated to save");
        return $heatmap_points;
    }

    public function getGridResolution() {
        return $this->getRequest()->getParam(
            'grid-res',$this->getDefaultGridResolution()
        );
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
