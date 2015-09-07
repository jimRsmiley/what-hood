<?php

namespace Whathood;

class Config extends \Zend\Config\Config {

    public function __construct(array $data) {
            parent::__construct($data);
    }

    public static function build(array $data) {
        $config = new Config($data);

        if (!$config->gridResolution())
            throw new \InvalidArgumentException(
                "grid_resolution must be defined whathood.yaml");
        return $config;
    }

    public function gridResolution() {
        if ($this->default_grid_resolution)
            return $this->default_grid_resolution;

        return $this->grid_resolution;
    }

    public function heatmapGridResolution() {
        return $this->heatmap_grid_resolution;
    }
}

