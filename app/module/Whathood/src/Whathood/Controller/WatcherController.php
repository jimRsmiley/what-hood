<?php

namespace Whathood\Controller;

use Whathood\Entity\NeighborhoodPolygon;

class WatcherController extends BaseController
{
    protected $_grid_resolution = 0.00008;
    #protected $_grid_resolution = 0.0008;

    protected $_concave_hull_target_precentage = 0.9;


    public function watchAction() {

        $force              = $this->getRequest()->getParam('force',false);
        $forever            = $this->getRequest()->getParam('forever',false);
        $neighborhood_name  = $this->getRequest()->getParam('neighborhood',null);
        $region_name        = $this->getRequest()->getParam('region',null);

        $this->logger()->info("Whathood watcher has started");

        $neighborhood_name = str_replace('+',' ',$neighborhood_name);
        do {
            $start_time = microtime(true);
            if ($neighborhood_name and $region_name ) {
                $user_polygons = $this->userPolygonMapper()->getByNeighborhood($neighborhood_name,$region_name);
            }
            else if ($force) {
                $user_polygons = $this->userPolygonMapper()->fetchAll();
            }
            else {
                $user_polygons = $this->userPolygonMapper()
                    ->getUserPolygonsNotAssociatedWithNeighborhoodPolygons();
            }

            if (!empty($user_polygons)) {
                foreach($user_polygons as $up) {
                    $this->logger()->info(sprintf("processing new user generated polygon(%s) for neighborhood %s",
                        $up->getId(),$up->getNeighborhood()->getName() ));
                }

                $elapsed_time_array = array();

                $neighborhoods = $this->collate_neighborhoods($user_polygons);
                foreach($neighborhoods as $n) {
                    $ups = $n->getUserPolygons();
                    $this->logger()->info(sprintf("rebuilding neighborhood(%s) %s with %s polygons",
                        $n->getId(),$n->getName(),count($ups)) );

                    try {
                        # start build
                        $timer = \Whathood\Timer::init();
                        $geojson = $this->neighborhoodPolygonMapper()->generateBorder(
                            $ups,
                            $n->getId(),
                            $this->getGridResolution(),
                            $this->getConcaveHullTargetPercentage()
                        );
                        $elapsed_seconds = $timer->elapsed_seconds();

                        # end build
                        $neighborhoodPolygon = new NeighborhoodPolygon( array(
                            'geojson' => $geojson,
                            'neighborhood' => $n,
                            'user_polygons' => $ups
                        ));
                        array_push($elapsed_time_array,$elapsed_seconds);
                        $this->logger()->info(sprintf("id=%s name=%s num_user_polygons=%s build_time=%s mins", $n->getId(), $n->getName(), $timer->elapsed_minutes() ));
                        $this->neighborhoodPolygonMapper()->save($neighborhoodPolygon);
                    }
                    catch(\Exception $e) {
                        $this->logger()->err($e->getMessage());
                        $err_msg = "FATAL: the watcher script died because of an error";
                        $this->logger()->err($err_msg);
                        die($err_msg);
                    }
                }
                $this->logger()->info(sprintf("average neighborhood build: %s",array_sum($elapsed_time_array)/count($elapsed_time_array)));
                $elapsed_seconds = microtime(true) - $start_time;
                $this->logger()->info(sprintf("total run seconds %s", $elapsed_seconds));
            }

            if ($forever)
                sleep(5);
        }
        while ($forever);
    }

    public function getGridResolution() {
        return $this->_grid_resolution;
    }

    public function getConcaveHullTargetPercentage() {
        return $this->_concave_hull_target_precentage;
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
