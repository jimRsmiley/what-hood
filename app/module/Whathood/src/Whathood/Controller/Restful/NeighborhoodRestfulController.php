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

    public function get($id) {
        die("not yet implemented");
    }

    public function dataTablesAction() {
        $start = $this->params()->fromQuery('start');
        $length = $this->params()->fromQuery('length');
        $draw = $this->params()->fromQuery('draw');

        $pageNum = $start / $length + 1;
        $item_count_per_page = $length;

        if (!$pageNum)
            $pageNum = 0;

        if (!$item_count_per_page)
            $item_count_per_page = 20;
        $query = $this->getServiceLocator()->get('mydoctrineentitymanager')
            ->createQueryBuilder()->select( array( 'n' ) )
            ->from('Whathood\Entity\Neighborhood', 'n')
            ->getQuery();

        $paginator = new \Whathood\View\Paginator\NeighborhoodPaginator(
                        new \Whathood\View\Paginator\NeighborhoodPaginatorAdapter($query)
                );
        $paginator->setDefaultItemCountPerPage($item_count_per_page);
        $paginator->setCurrentPageNumber($pageNum);

        $userPolygons = $paginator->getCurrentItems();
        $data = array();
        foreach ($userPolygons as $up) {
            array_push($data, array(
                    $up->getId(),
                    "",
                    $up->getName(),
                    $up->getRegion()->getName()
                )
            );
        }

        return new JsonModel(array(
            'data'              => $data,
            "recordsTotal"      => $paginator->getTotalItemCount(),
            "recordsFiltered"   => $paginator->getTotalItemCount()
        ));
    }

}
