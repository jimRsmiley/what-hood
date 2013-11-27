<?php
namespace Application\Controller;

use Zend\View\Model\ViewModel;
use Application\Spatial\PHP\Types\Geometry\Polygon;
use Application\Spatial\PHP\Types\Geometry\FeatureCollection;
use Application\Spatial\PHP\Types\Geometry\Feature;
use Application\Spatial\PHP\Types\Geometry\LineString;
use Application\Entity\Neighborhood;
use Application\Entity\Region;
/**
 * Description of NeighborhoodController
 *
 * @author Jim Smiley twitter:@jimRsmiley
 */
class HeatMapController extends BaseController
{
    protected $heatMapMapper = null;
    
    /*
     * I want to be able to visualize neighborhood bounds
     */
    public function showNeighborhoodBoundsAction() {
        $regionName = $this->getUriParameter('regionName');
        $neighborhoodName = $this->getUriParameter('neighborhoodName');
        
        if( empty( $regionName ) )
            throw new \InvalidArgumentException('regionName may not be null');
        
        if( empty( $neighborhoodName ) )
            throw new \InvalidArgumentException('neighborhoodName may not be null');
        
        $regionName         = str_replace('_', ' ', $regionName );
        $neighborhoodName   = str_replace('_', ' ', $neighborhoodName );
        
        $neighborhoodPolygons = $this->neighborhoodPolygonMapper()
                ->byNeighborhoodName($neighborhoodName,$regionName);
        
        $heatMapTool = $this->getServiceLocator()
                ->get('Application\Model\HeatMap\HeatMapBuilder');
        
        $boundarySquare = $heatMapTool->getBoundarySquare($neighborhoodPolygons);
        $lineString = $boundarySquare->getLineString();
        $lineString->close();
        
        $featureCollection = new FeatureCollection();
        $featureCollection->addGeometry( 
            new Feature( new Polygon( array($lineString) )
        ));
        
        /*
         * add the neighborhoodPolygons
         */
        foreach( $neighborhoodPolygons as $np ) {
            $featureCollection->addGeometry( new Feature(
                    $np ) );
        }
        
        return $this->getViewModel( array(
            'featureCollection' => $featureCollection
        ));
    }
    
    public function showAction() {
        $regionName       = $this->getUriParameter('region_name');
        $neighborhoodName = $this->getUriParameter('neighborhood_name');
        $format = $this->getUriParameter('format');
        
        $regionName = str_replace('_', ' ', $regionName );
        $neighborhoodName = str_replace('_', ' ', $neighborhoodName );
        
        
        $neighborhood = new Neighborhood( array(
            'name' => $neighborhoodName,
            'region' => new Region( array(
                'name'  => $regionName
            ))
        ));
        
        try {
            $heatMap = $this->heatMapMapper()->getLatestHeatMap($neighborhood);

            if( $format == 'json' ) {
                return new JsonModel( $heatMap->toJsonArray() );
            } else {
                return $this->getViewModel( array(
                    'heatMap' => $heatMap,
                ));
            }
        }
        catch( \Doctrine\ORM\NoResultException $e ) {
            
            $viewModel = new ViewModel( array(
                'message' => 'No heatmaps exist for ' . $neighborhoodName . " " . $regionName
            ));
            
            $viewModel->setTemplate( 'application/show-message.phtml' );
            return $viewModel;
        }
        
        
    }
    
    public function heatMapMapper() {
        if( $this->heatMapMapper == null ) {
            $this->heatMapMapper = $this->getServiceLocator()
                                    ->get('Application\Mapper\HeatMapMapper');
        }
        return $this->heatMapMapper;
    }
}

?>
