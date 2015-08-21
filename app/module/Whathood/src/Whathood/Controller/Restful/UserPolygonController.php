<?php
namespace Whathood\Controller\Restful;

use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\View\Model\JsonModel;
use Whathood\Entity\UserPolygon;

class UserPolygonController extends BaseController {

    protected $collectionOptions = array('GET','POST');
    protected $resourceOptions = array('GET','PUT','DELETE');

    public function getAction() {
        $id = $this->params()->fromRoute('id');
        if (empty($id) or $id=='null')
            return $this->badRequestJson("id may not be empty");
        try {
            $up = $this->m()->userPolygonMapper()->byId($id);

            $this->logger()->info("user-polygon REST served GET user-polygon $id");

            if (!$up)
                return $this->badRequestJson("no user-polygon found with id $id");
            return new JsonModel( $up->toArray() );
        }
        catch(\Exception $e) {
            return $this->badRequestJson("server-error:".$e->getMessage()."\n\n\n\n\n".$e->getTraceAsString());
        }
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
            ->createQueryBuilder()->select( array( 'up' ) )
            ->from('Whathood\Entity\UserPolygon', 'up')
            ->orderBy('up.dateTimeAdded','DESC')
            ->getQuery();

        $paginator = new \Whathood\View\Paginator\UserPolygonPaginator(
                        new \Whathood\View\Paginator\UserPolygonPaginatorAdapter($query)
                );
        $paginator->setDefaultItemCountPerPage($item_count_per_page);
        $paginator->setCurrentPageNumber($pageNum);

        $userPolygons = $paginator->getCurrentItems();
        $data = array();
        foreach ($userPolygons as $up) {
            array_push($data, array(
                    $up->getId(),
                    $up->getDateTimeAdded(),
                    $up->getNeighborhood()->getName()
                )
            );
        }


        return new JsonModel(array(
            'data'              => $data,
            "recordsTotal"      => $paginator->getTotalItemCount(),
            "recordsFiltered"   => $paginator->getTotalItemCount()
        ));
    }

    public function listAction() {
        $pageNum = $this->params()->fromRoute('page');
        $item_count_per_page = $this->params()->fromRoute('count_per_page');

        if (!$pageNum)
            $pageNum = 0;

        if (!$item_count_per_page)
            $item_count_per_page = 20;
		$query = $this->m()->userPolygonMapper()->getPaginationQuery();

        $paginator = new \Whathood\View\Paginator\UserPolygonPaginator(
                        new \Whathood\View\Paginator\UserPolygonPaginatorAdapter($query)
                );
        $paginator->setDefaultItemCountPerPage($item_count_per_page);
        $paginator->setCurrentPageNumber($pageNum);

        $userPolygons = $paginator->getCurrentItems();

        $arr = UserPolygon::polygonsToArray(
            $userPolygons, array('strings_only' => true, 'include_neighborhood' => true));
        return new JsonModel(array('user_polygons' => $arr));
    }

    public function getList() {
        return new JsonModel( array(
            'msg' => 'not yet implemented' ));
    }
}
