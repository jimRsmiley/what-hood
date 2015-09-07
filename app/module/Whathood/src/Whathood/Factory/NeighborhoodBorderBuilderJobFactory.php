<?php

namespace Whathood\Factory;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class NeighborhoodBorderBuilderJobFactory implements FactoryInterface {

    public function createService(ServiceLocatorInterface $sl)
    {
        $serviceLocator = $sl->getServiceLocator();
        $config = $serviceLocator->get('Whathood\Config');

        $job = new \Whathood\Job\NeighborhoodBorderBuilderJob(array(
            'gridResolution'        => $config->gridResolution(),
            'heatmapGridResolution' => $config->heatmapGridResolution(),
            'mapperBuilder'         => $serviceLocator->get('Whathood\Mapper\Builder'),
            'logger'                => $serviceLocator->get('Whathood\Logger')
        ));
        return $job;
    }
}
