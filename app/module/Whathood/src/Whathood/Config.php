<?php

namespace Whathood;

class Config extends \Zend\Config\Config {

    public function __construct(array $data) {
            parent::__construct($data);
    }

    public function gridResolution() {
        return $this->grid_resolution;
    }

    public function heatmapGridResolution() {
        return $this->heatmap_grid_resolution;
    }
}

