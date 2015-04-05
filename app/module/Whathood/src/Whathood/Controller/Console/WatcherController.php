<?php

namespace Whathood\Controller\Console;

use Whathood\Controller\BaseController;
use Whathood\Entity\NeighborhoodPolygon;
use Whathood\Timer;

class WatcherController extends BaseController
{
    protected $_DEFAULT_GRID_RESOLUTION = 0.0002;

    protected $_concave_hull_target_precentage = 0.9;


    public function watchAction() {
        $api_timer = Timer::start('api');

        $force                  = $this->getRequest()->getParam('force',false);
        $forever                = $this->getRequest()->getParam('forever',false);
        $neighborhood_name      = $this->getRequest()->getParam('neighborhood',null);
        $region_name            = $this->getRequest()->getParam('region',null);
        $this->_grid_resolution = $this->getRequest()->getParam('grid-res',$this->_DEFAULT_GRID_RESOLUTION);

        $this->logger()->info("Whathood watcher has started");
        $this->logger()->info(
            sprintf("\tgrid-resolution=%g target-precision=%s",
                $this->getGridResolution(),
                $this->getTargetPercentage()
            )
        );

        $neighborhood_name = str_replace('+',' ',$neighborhood_name);
        do {
            if ($neighborhood_name and $region_name ) {
                $neighborhood = $this->m()->neighborhoodMapper()
                    ->byName($neighborhood_name,$region_name);
                $user_polygons = $this->userPolygonMapper()
                    ->byNeighborhood($neighborhood);
            }
            else if ($force) {
                $user_polygons = $this->userPolygonMapper()->fetchAll();
            }
            else {
                $up_t = Timer::start('gather_user_polygons');
                $user_polygons = $this->userPolygonMapper()
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
                        $polygon = $this->m()->electionMapper()->generateBorderPolygon(
                            $ups,
                            $n->getId(),
                            $this->getGridResolution(),
                            $this->getConcaveHullTargetPercentage()
                        );
                        $timer->stop();

                        if (!$polygon) {
                            $this->logger()->warn("Could not construct a neighborhood border for ".$n->getName());
                            continue;
                        }

                        $neighborhoodPolygon = NeighborhoodPolygon::build( array(
                            'geom' => $polygon,
                            'neighborhood' => $n,
                            'user_polygons' => $ups
                        ));
                        $this->logger()->info(
                            sprintf("\tid=%s name=%s num_user_polygons=%s build_time=%s mins",
                                $n->getId(),
                                $n->getName(),
                                count($ups),
                                $timer->elapsed_minutes()
                            )
                    );
                        $this->neighborhoodPolygonMapper()->save($neighborhoodPolygon);
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

        #$this->logger()->info(Timer::report_str());
    }

    public function getGridResolution() {
        return $this->_grid_resolution;
    }

    public function getTargetPercentage() {
        return $this->getConcaveHullTargetPercentage();
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
