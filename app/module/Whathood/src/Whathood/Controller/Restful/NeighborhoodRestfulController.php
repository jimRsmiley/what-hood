<?php

namespace Whathood\Controller\Restful;

use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\View\Model\JsonModel;

/**
 *
 * serve neighborhood REST data
 *
 */
class NeighborhoodRestfulController extends BaseController {

    use DataTablesControllerTrait;

    public function get($id) {
        die("not yet implemented");
    }

    public function getQuery() {
        return $this->getServiceLocator()->get('mydoctrineentitymanager')
            ->createQueryBuilder()->select( array( 'n' ) )
            ->from('Whathood\Entity\Neighborhood', 'n')
            ->getQuery();
    }

}
