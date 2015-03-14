<?php

namespace Whathood\Controller;

use Zend\View\Model\ViewModel;

class NeighborhoodController extends BaseController {

    public function showAction() {
        $region_name = $this->params()->fromRoute('region');
        $neighborhood_name = $this->params()->fromRoute('neighborhood');

        if (empty($neighborhood_name) or empty($region_name))
            throw new \InvalidArgumentException("neighborhood_name and region_name must be defined");

        $neighborhood = $this->neighborhoodMapper()->byName($neighborhood_name,$region_name);

        $user_polygon_count = $this->neighborhoodPolygonMapper()
            ->latestByNeighborhoodId($neighborhood->getId())
            ->userPolygonCount();

        return new ViewModel( array(
            'neighborhood' => $neighborhood,
            'user_polygon_count' => $user_polygon_count
        ));
    }

    public function deleteAction() {
        $neighborhood_id = $this->params()->fromRoute('id');
        $neighborhood_name = $this->paramFromRoute('neighborhood');
        $region_name = $this->paramFromRoute('region');

        $this->logger()->info("deleting neighborhood with id ".$neighborhood_id);

        if ($neighborhood_id) {
            $neighborhood = $this->neighborhoodMapper()->byId($neighborhood_id);

            $this->prompt_user(
                sprintf("are you sure you want to delete neighborhood?\n\tid=%s\n\tname=%s\n\tregion=%s\n",
                    $neighborhood->getId(),
                    $neighborhood->getName(),
                    $neighborhood->getRegion()->getName()
                )
            );


            $this->neighborhoodMapper()->delete(
                $neighborhood,$this->userPolygonMapper(),$this->neighborhoodPolygonMapper());
            $this->logger()->info("neighborhood deleted");
        }
        else {
            throw new \Exception('not yet implemented');
        }
    }
}
