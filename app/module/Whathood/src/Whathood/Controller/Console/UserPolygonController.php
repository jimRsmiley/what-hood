<?php
namespace Whathood\Controller\Console;

use Zend\Json\Json;
use Zend\View\Model\ViewModel;
use Whathood\Spatial\PHP\Types\Geometry\MultiPoint as WhMultiPoint;

class UserPolygonController extends AbstractController
{
    /**
     * print JUST the number of total user polygons in the system
     **/
    public function numUserNeighborhoodsAction() {
        return (string)$this->m()->userPolygonMapper()
                                    ->numUserPolygons();
    }
}
