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
}
