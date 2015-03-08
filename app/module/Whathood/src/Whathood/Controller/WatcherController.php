<?php

namespace Whathood\Controller;

use Whathood\Entity\NeighborhoodPolygon;

class WatcherController extends BaseController
{
    # Fishtown in 88 seconds
    #protected $_grid_resolution = 0.001;

    # Fishtown from 504 seconds to 115
    protected $_grid_resolution = 0.0005;

    public function watchAction() {

        $force              = $this->getRequest()->getParam('force',false);
        $forever            = $this->getRequest()->getParam('forever',false);
        $neighborhood_name  = $this->getRequest()->getParam('neighborhood',null);
        $region_name        = $this->getRequest()->getParam('region',null);

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

            if (empty($user_polygons)) {
                $this->logger()->info("no user polygons were found without neighborhood polygons associated");
            }
            else {
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
                            $this->getGridResolution());
                        $elapsed_time = $timer->elapsed_seconds();

                        # end build
                        $neighborhoodPolygon = new NeighborhoodPolygon( array(
                            'geojson' => $geojson,
                            'neighborhood' => $n,
                            'user_polygons' => $ups
                        ));
                        array_push($elapsed_time_array,$elapsed_time);
                        $this->logger()->info(sprintf("id=%s name=%s build_secs=%s", $n->getId(), $n->getName(), $elapsed_time ));
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
