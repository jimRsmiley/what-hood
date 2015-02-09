<?php

namespace Whathood\Controller;

use Whathood\Entity\NeighborhoodPolygon;

class WatcherController extends BaseController
{
    public function watchAction() {

        $GRID_RESOLUTION = 0.002;

        while(true) {
            $user_polygons = $this->userPolygonMapper()
                ->getUserPolygonsNotAssociatedWithNeighborhoodPolygons();

            if (empty($user_polygons)) {
                $this->logger()->info("no user polygons were found without neighborhood polygons associated");
            }
            else {
                foreach($user_polygons as $up) {
                    $this->logger()->info(sprintf("processing new user generated polygon(%s) for neighborhood %s",
                        $up->getId(),$up->getNeighborhood()->getName() ));
                }

                $neighborhoods = $this->collate_neighborhoods($user_polygons);
                foreach($neighborhoods as $n) {
                    $ups = $n->getUserPolygons();
                    $this->logger()->info(sprintf("rebuilding neighborhood(%s) %s with %s polygons",
                        $n->getId(),$n->getName(),count($ups)) );

                    try {
                        $geojson = $this->neighborhoodPolygonMapper()->generateBorder(
                            $ups,
                            $n->getId(),
                            $GRID_RESOLUTION);

                        $neighborhoodPolygon = new NeighborhoodPolygon( array(
                            'geojson' => $geojson,
                            'neighborhood' => $n,
                            'user_polygons' => $ups
                        ));
                        $this->logger()->info("saving new neighborhood polygon");
                        $this->neighborhoodPolygonMapper()->save($neighborhoodPolygon);
                        $this->logger()->info("done saving");
                    }
                    catch(\Exception $e) {
                        $this->logger()->err($e->getMessage());
                    }
                }
            }
            sleep(5);
        }
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
