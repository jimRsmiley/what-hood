<?php

namespace Whathood\Controller;

use Zend\View\Model\ViewModel;

class NeighborhoodController extends BaseController {

    /**
     * show a neighborhood by name and region name
     */
    public function showAction() {
        $region_name        = $this->params()->fromRoute('region');
        $neighborhood_name  = $this->params()->fromRoute('neighborhood');

        if (empty($neighborhood_name) or empty($region_name))
            throw new \InvalidArgumentException("neighborhood_name and region_name must be defined");

        try {
            $neighborhood = $this->m()->neighborhoodMapper()
                ->byName($neighborhood_name,$region_name);
        } catch(\Exception $e) {
            $viewModel = new ViewModel(array(
                'message' => sprintf("No neighborhood named '%s' in region '%s' found",
                    $neighborhood_name,
                    $region_name )
            ));
            $viewModel->setTemplate('whathood/neighborhood/error_no_neighborhood.phtml');
            return $viewModel;
        }

        if (empty($neighborhood))
            throw new \Exception("no neighborhood was returned");

        $latest_np = null;

        try {
            $latest_np = $this->m()->neighborhoodPolygonMapper()
                ->latestByNeighborhood($neighborhood);
            $user_polygon_count = $latest_np->userPolygonCount();
        }
        catch(\Exception $e) {
            $user_polygon_count = 0;
        }

        $userPolygons = $this->m()->userPolygonMapper()->byNeighborhood($neighborhood);
        return new ViewModel( array(
            'neighborhood'       => $neighborhood,
            'neighborhood_polygon' => $latest_np,
            'user_polygon_count' => count($userPolygons)
        ));
    }

    public function byIdAction() {
        $neighborhood_id = $this->params()->fromRoute("id");

        if (empty($neighborhood_id))
            throw new \InvalidArgumentException("neighborhood_id must be defined");

        try {
            $neighborhood = $this->m()->neighborhoodMapper()
                ->byId($neighborhood_id);
        } catch(\Exception $e) {
            $viewModel = new ViewModel(array(
                'message' => sprintf("No neighborhood with id %s",
                    $neighborhood_id )
            ));
            $viewModel->setTemplate('whathood/neighborhood/error_no_neighborhood.phtml');
            return $viewModel;
        }

        if (empty($neighborhood))
            throw new \Exception("no neighborhood was returned");

        $latest_np = null;

        try {
            $latest_np = $this->m()->neighborhoodPolygonMapper()
                ->latestByNeighborhood($neighborhood);
            $user_polygon_count = $latest_np->userPolygonCount();
        }
        catch(\Exception $e) {
            $user_polygon_count = 0;
        }

        $userPolygons = $this->m()->userPolygonMapper()->byNeighborhood($neighborhood);
        $viewModel = new ViewModel( array(
            'neighborhood'       => $neighborhood,
            'neighborhood_polygon' => $latest_np,
            'user_polygon_count' => count($userPolygons)
        ));
        $viewModel->setTemplate('whathood/neighborhood/show.phtml');
        return $viewModel;
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

    public function listAction() {}
}
