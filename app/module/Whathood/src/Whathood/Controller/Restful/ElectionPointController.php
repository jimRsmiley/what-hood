<?php
namespace Whathood\Controller\Restful;

use Zend\View\Model\JsonModel;
use CrEOF\Spatial\PHP\Types\Geometry\Point;

/**
 * serve restful requests for election points
 */
class ElectionPointController extends BaseController {

    public function get($id) {
        die("not yet implemented");
    }

    /**
     * can handle queries of:
     *  - x and y
     */
    public function getList() {
        $x = $this->params()->fromRoute('x');
        $y = $this->params()->fromRoute('y');

        $this->logger()->info("whathood REST get xy=$x,$y");
        $point = new Point($x,$y);
        $electionPoint = $this->m()->userPolygonMapper()->getElectionPoint($point);

        if (null == $electionPoint)
            return $this->badRequestJson("no user polygons found with point");

        return new JsonModel($electionPoint->toArray());
    }

}
