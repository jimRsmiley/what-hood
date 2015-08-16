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

        $this->logger()->debug("Whathood watcher has started");
        $this->logger()->debug("grid-resolution: ".rtrim(sprintf("%.8F",$this->getGridResolution()),"0"));

        $neighborhood_name = str_replace('+',' ',$neighborhood_name);
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
                    $electionCollection = $this->m()->pointElectionMapper()->getCollection(
                        $ups,
                        $n->getId(),
                        $this->getGridResolution()
                    );

                    if (empty($electionCollection->getPointElections())) {
                       $this->logger()->warn("electionCollection contains no points");
                    }
                    else {
                        try {
                            if ($this->buildAndSaveNeighborhoodPolygon($electionCollection, $n, $ups)) {
                                $this->logger()->info(
                                    sprintf("\t\tsaved neighborhood polygon elapsed=%s", $timer->elapsedReadableString()));

                                $this->buildAndSaveHeatmapPoints($ups, $n, $this->getHeatmapGridResolution());
                            }
                            else {
                                $this->logger()->err("did not get a neighborhood polygon");
                            }
                        }
                        catch(\Whathood\Exception $e) {
                            $this->logger()->err("Failed to build polygon for ".$n->getName().": ". $e->getMessage());
                        } catch(\Exception $e) {
                            $this->logger()->err("big error trying to build neighborhood polygon");
                            $this->logger()->err(get_class($e));
                            $this->logger()->err($e);
                        }
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
                $this->logger()->err(
                    sprintf("memory: %smb",
                        \Whathood\Util::memory_usage()
                    )
                );
            } // foreach neighborhood
        } // if there are user polygons
        $this->logger()->info("watcher finished");
    }

    public function logFoundUserBorders($user_polygons) {
        foreach($user_polygons as $up) {
            $this->logger()->debug(
                sprintf("\tfound new user generated polygon(%s) for neighborhood %s",
                    $up->getId(),
                    $up->getNeighborhood()->getName()
                )
            );
        }
    }

    public function buildAndSaveNeighborhoodPolygon(PointElectionCollection $electionCollection, Neighborhood $n,$ups) {
        $polygon = $this->m()->pointElectionMapper()->generateBorderPolygon(
            $electionCollection, $n
        );

        if (!$polygon) {
            $this->logger()->warn("Could not construct a neighborhood border for ".$n->getName());
            return;
        }

        $neighborhoodPolygon = NeighborhoodPolygon::build( array(
            'geom' => $polygon,
            'neighborhood' => $n,
            'user_polygons' => $ups,
            'grid_resolution' => $this->getGridResolution()
        ));
        $this->m()->neighborhoodPolygonMapper()->save($neighborhoodPolygon);
        $this->m()->neighborhoodPolygonMapper()->detach($neighborhoodPolygon);
        return $neighborhoodPolygon;
    }

    /**
     * take the user polygons, run a point election, and save the points in the db
     **/
    public function buildAndSaveHeatmapPoints($user_polygons, Neighborhood $n, $grid_resolution) {

        $timer = Timer::start("heatmap_builder");
        $electionCollection = $this->m()->pointElectionMapper()
            ->getCollection(
                $user_polygons,
                $n->getId(),
                $grid_resolution
            );

        $heatmap_points = $electionCollection->heatMapPointsByNeighborhood($n);

        if (!empty($heatmap_points)) {
            $this->m()->heatMapPoint()->deleteByNeighborhood($n);
            $this->m()->heatMapPoint()->savePoints($heatmap_points);
            $this->m()->heatMapPoint()->detach($heatmap_points);
            $this->logger()->info(
                sprintf("\t\tsaved %s heatmap points from %s points elapsed=%s",
                    count($heatmap_points), count($electionCollection->getPointElections()), $timer->elapsedReadableString()
                )
            );
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

    public function getDefaultGridResolution() {
        $config = $this->getServiceLocator()->get('Whathood\YamlConfig');
        if (!array_key_exists('default_grid_resolution',$config))
            throw new \Exception('default_grid_resolution not found in yaml config file');
        return $config['default_grid_resolution'];
    }

    public function getHeatmapGridResolution() {
        $key = 'heatmap_grid_resolution';
        $config = $this->getServiceLocator()->get('Whathood\YamlConfig');
        if (!array_key_exists($key,$config))
            throw new \Exception("$key not found in yaml config file");
        return $config[$key];
    }

    /**
     * we can have multiple user polygons with the same neighborhood, so just return one neighborhood
     * and order the neighborhoods so the ones with the oldest polygons go first
     */
    public function collate_neighborhoods(array $user_polygons) {
        $neighborhoood_array = array();

        foreach($user_polygons as $up) {
            $neighborhood_array[$up->getNeighborhood()->getId()] = $up->getNeighborhood();
        }

        $neighborhoods = array_values($neighborhood_array);
        usort($neighborhoods,array($this,'sortNeighborhoodsByYoungestPolygon'));
        return $neighborhoods;
    }


    /**
     * returns 1 if n1 has a younger(or no) polygon than n2, else if n2 is younger, returns -1, if they're equal, returns 0
    **/
    public function sortNeighborhoodsByYoungestPolygon(Neighborhood $n1, Neighborhood $n2) {
        $mapper = $this->m()->neighborhoodPolygonMapper();
        $np1 = $mapper->latestByNeighborhood($n1);
        $np2 = $mapper->latestByNeighborhood($n2);
        if ($np1 == null and $np2 == null)
            return 0;
        else if ($np1 == null and $np2)
            return 1;
        else if ($np1 and $np2 == null)
            return -1;
        else if ($np1->getCreatedAt() < $np2->getCreatedAt())
            return 1;
        else if ($np1->getCreatedAt() > $np2->getCreatedAt())
            return -1;
        return 0;
    }

}
