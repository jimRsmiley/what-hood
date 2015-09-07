<?php
namespace Whathood\Controller\Restful;

use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use Zend\Mvc\Controller\AbstractRestfulController;

use Whathood\Spatial\PHP\Types\Geometry\FeatureCollection;
use Whathood\Spatial\PHP\Types\Geometry\Feature;

/**
 * Description of RegionController
 *
 * @author Jim Smiley twitter:@jimRsmiley
 */
class RegionRestController extends AbstractRestfulController {

    public function get($id) {
        $region = $this->getRegionMapper()->byId($id);

        return new JsonModel( array( "region" => $region->toArray()  ) );
    }

    public function create($data) {

    }

    public function updateAction() {

    }

    public function update($id,$data) {

    }

    public function delete($id) {

    }
}

?>
