<?php

namespace Whathood\Factory;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class EmailJobFactory implements FactoryInterface {

    public function createService(ServiceLocatorInterface $sl)
    {
        $emailer = $sl->getServiceLocator()->get('Whathood\Emailer');

        $job = new \Whathood\Job\EmailJob($emailer);
        return $job;
    }

}
