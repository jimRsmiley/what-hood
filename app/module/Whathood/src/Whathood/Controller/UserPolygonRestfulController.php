<?php
namespace Whathood\Controller;

use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\View\Model\JsonModel;

class UserPolygonRestfulController extends BaseRestfulController {

    protected $collectionOptions = array('GET','POST');
    protected $resourceOptions = array('GET','PUT','DELETE');

    public function get($id) {

        try {
            $up = $this->userPolygonMapper()->byId($id);

            $this->logger()->info("user-polygon REST served GET user-polygon $id");

            if (!$up)
                return $this->badRequestJson("no user-polygon found with id $id");
            return new JsonModel( $up->toArray() );
        }
        catch(\Exception $e) {
            return $this->badRequestJson("server-error:".$e->getMessage()."\n\n\n\n\n".$e->getTraceAsString());
        }
    }

    public function getList() {
        return new JsonModel( array(
            'msg' => 'not yet implemented' ));
    }
}
