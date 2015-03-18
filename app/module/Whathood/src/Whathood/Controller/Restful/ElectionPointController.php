<?php
namespace Whathood\Controller;

use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\View\Model\JsonModel;
use Whathood\Model\Whathood\WhathoodConsensus as Consensus;

class WhathoodRestfulController extends BaseRestfulController {


    public function get($id) {
        die("not yet implemented");
    }

    public function getList() {
        $x = $this->params()->fromRoute('x');
        $y = $this->params()->fromRoute('y');

        $this->logger()->info("whathood REST get xy=$x,$y");
        $whathood_result = $this->userPolygonMapper()->getWhathoodResult($x,$y);

        return new JsonModel($whathood_result->toArray());
    }

}
