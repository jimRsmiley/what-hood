<?php

namespace Whathood\Factory;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class NeighborhoodBorderBuilderJobFactory implements FactoryInterface {

    public function createService(ServiceLocatorInterface $sl)
    {
        if ($sl instanceof \SlmQueue\Job\JobPluginManager)
            $serviceLocator = $sl->getServiceLocator();
        else
            $serviceLocator = $sl;

        $config = $serviceLocator->get('Whathood\Config');

        $job = \Whathood\Job\NeighborhoodBorderBuilderJob::build(array(
            'gridResolution'        => $config->gridResolution(),
            'mapperBuilder'         => $serviceLocator->get('Whathood\Mapper\Builder'),
            'logger'                => $serviceLocator->get('Whathood\Logger'),
            'queue'                 => $serviceLocator->get('Whathood\Service\MessageQueue')
        ));
        return $job;
    }
}
