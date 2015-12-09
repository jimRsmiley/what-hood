<?php
namespace Whathood\Controller\Restful;

use Zend\View\Model\JsonModel;
use CrEOF\Spatial\PHP\Types\Geometry\Point;
use Whathood\Election\PointElection;

/**
 * serve restful requests for election points
 */
class PointElectionController extends BaseController {

    public function get($id) {
        die("not yet implemented");
    }

    /**
     * can handle queries of:
     *  - x and y
     */
    public function getListAction() {
        $x = $this->params()->fromRoute('x');
        $y = $this->params()->fromRoute('y');

        $this->logger()->info("whathood REST get xy=$x,$y");
        $point = new Point($x,$y);
        $userPolygons = $this->m()->userPolygonMapper()
            ->byPoint($point);

        if (empty($userPolygons)) {
            $userPolygons = array();
            return $this->badRequestJson(
              array('msg' => "no neighborhoods at this location"));
        }
        else {
          $electionPoint = PointElection::Build(array(
              'point' => $point,
              'user_polygons' => $userPolygons,
              'logger' => $this->logger() ));

          return new JsonModel($electionPoint->toArray());
        }
    }

}
