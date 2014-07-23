<?php
namespace Whathood\Controller;

use Zend\View\Model\ViewModel;
use Whathood\Spatial\PHP\Types\Geometry\Polygon;
use Whathood\Spatial\PHP\Types\Geometry\FeatureCollection;
use Whathood\Spatial\PHP\Types\Geometry\Feature;
use Whathood\Spatial\PHP\Types\Geometry\LineString;
use Whathood\Entity\Neighborhood;
use Whathood\Entity\Region;
use Whathood\View\Model\JsonModel;
use Whathood\Mapper\CreateEventMapper;
/*
* region borders are created during create events, when all the user
* polygons are counted and borders are drawn,
*
* this controller handles the display of create events
*
*/

class CreateEventController extends BaseController {

    public function listAction() {
        $createEvents = $this->createEventMapper()->fetchAll();

        return new ViewModel( array( 'createEvents' => $createEvents ) );
    }
}