<?php
namespace Whathood\Controller;

use Zend\View\Model\ViewModel;
use Whathood\Spatial\PHP\Types\Geometry\Polygon;
use Whathood\Spatial\PHP\Types\Geometry\FeatureCollection;
use Whathood\Spatial\PHP\Types\Geometry\Feature;
use Whathood\Spatial\PHP\Types\Geometry\LineString;
use Whathood\Entity\Neighborhood;
use Whathood\Entity\Region;
/**
 * Description of NeighborhoodController
 *
 * @author Jim Smiley twitter:@jimRsmiley
 */
class HeatMapController extends BaseController
{
    public function showAction() {
        $regionName       = $this->getUriParameter('region_name');
        $neighborhoodName = $this->getUriParameter('neighborhood_name');
        $format = $this->getUriParameter('format');
        
        $regionName = str_replace('_', ' ', $regionName );
        $neighborhoodName = str_replace('_', ' ', $neighborhoodName );
        
        try {

            if( $format == 'json' ) {
                return new JsonModel( $heatMap->toJsonArray() );
            } else {
                $neighborhood = $this->neighborhoodMapper()
                    ->getNeighborhoodByName($neighborhoodName,$regionName);
                
                $heatMap = $this->neighborhoodStrengthOfIdentityMapper()
                                    ->getHeatMapByNeighborhood($neighborhood);
                
                $jsonData = array();
                foreach( $heatMap as $r ) {
                    $arr = array( 
                        'lat' => $r['y'], 
                        'lon' => $r['x'], 
                        'value' => $r['strength_of_identity'] * 100 );
                    array_push( $jsonData, $arr);
                }
                return $this->getViewModel( array(
                    'heatMap' => $heatMap,
                    'heatMapJson' => \Zend\Json\Json::encode( $jsonData )
                ));
            }
        }
        catch( \Doctrine\ORM\NoResultException $e ) {
            
            return new ErrorViewModel( array( 'message' => 
                'No heatmaps exist for ' . $neighborhoodName . " " . $regionName ) );
        }
    }
    
    public function showLatestRegionNeighborhoodBordersAction() {
        
        $philadelphiaRegionId = 1;
        $geoJson = $this->neighborhoodStrengthOfIdentityMapper()
                ->getLatestNeighborhoodBordersByRegionIdAsGeoJson($philadelphiaRegionId);
        
        \Zend\Debug\Debug::dump( $geoJson );
        exit;
    }
}

?>
