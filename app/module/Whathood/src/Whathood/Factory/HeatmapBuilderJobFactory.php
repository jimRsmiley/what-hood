<?php

namespace Whathood\Factory;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class HeatmapBuilderJobFactory implements FactoryInterface {

    public function createService(ServiceLocatorInterface $sl)
    {
        if ($sl instanceof \SlmQueue\Job\JobPluginManager)
            $serviceLocator = $sl->getServiceLocator();
        else
            $serviceLocator = $sl;

        $config = $serviceLocator->get('Whathood\Config');

        $job = \Whathood\Job\HeatmapBuilderJob::build(array(
            'gridResolution'        => $config->heatmapGridResolution(),
            'mapperBuilder'         => $serviceLocator->get('Whathood\Mapper\Builder'),
            'logger'                => $serviceLocator->get('Whathood\Logger')
        ));
        return $job;
    }
}
