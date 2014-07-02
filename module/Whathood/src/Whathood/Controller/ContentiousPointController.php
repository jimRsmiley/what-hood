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
/**
 * Description of NeighborhoodController
 *
 * @author Jim Smiley twitter:@jimRsmiley
 */
class ContentiousPointController extends BaseController
{
    public function byCreateEventIdAction() {
        
        $createEventId  = $this->getUriParameter('create_event_id');
        $format         = $this->getUriParameter('format');
        
        if( $format == 'heatmapJsData' ) {
            try {
                $points = $this->contentiousPointMapper()
                        ->contentiousPointsByCreateEventId($createEventId);

                $heatmapJsData = $this->contentiousPointMapper()
                        ->pointsToHeatmapJsData( $points );

                //\Zend\Debug\Debug::dump( $heatmapJsData );

                return new JsonModel( $heatmapJsData->toJson() );
            }
            catch( Exception $e ) {
                return new ErrorViewModel( array( 'message' => 
                    'No heatmaps exist for ' . $neighborhoodName 
                    . " " . $regionName ) );
            }
        }
    }
}

?>
