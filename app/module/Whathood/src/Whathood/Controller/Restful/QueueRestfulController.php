<?php

namespace Whathood\Controller\Restful;

use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\View\Model\JsonModel;
use Whathood\Controller\Restful\DataTablesControllerTrait;

class QueueRestfulController extends BaseController {
    use DataTablesControllerTrait;
    public function get($id) {
        die("not yet implemented");
    }
    public function getQuery() {
        return $this->getServiceLocator()->get('doctrine.entitymanager.orm_default')
            ->createQueryBuilder()->select( array( 'entity' ) )
            ->from('Whathood\Entity\DefaultQueue', 'entity')
            ->getQuery();
    }
}
