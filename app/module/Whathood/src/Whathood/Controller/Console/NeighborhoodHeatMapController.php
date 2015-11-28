<?php

namespace Whathood\Controller\Console;

use Whathood\Controller\BaseController;
use Whathood\Entity\NeighborhoodBoundary;
use Whathood\Timer;

class NeighborhoodHeatMapController extends BaseController
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
        $this->setTargetPrecision(
            $this->getRequest()->getParam(
                'target-precision',$this->getDefaultTargetPrecision()
            ));

        $this->logger()->info("Whathood watcher has started");
        $this->logger()->info(
            sprintf("\tgrid-resolution=%g target-precision=%s",
                $this->getGridResolution(),
                $this->getTargetPercentage()
            )
        );

        $neighborhood_name = str_replace('+',' ',$neighborhood_name);

        $test_points = $this->m()->testPointMapper()->createByUserPolygons($user_polygons,$grid_resolution);
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
