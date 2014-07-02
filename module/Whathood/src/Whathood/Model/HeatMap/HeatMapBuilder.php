<?php //
namespace Whathood\Model\HeatMap;

use Whathood\Entity\Neighborhood;
use Whathood\Entity\HeatMap;
use Whathood\Entity\Region;
use Whathood\Model\Whathood\EntityConsensus;
use Whathood\Entity\NeighborhoodHeatMapPoint as HeatMapPoint;
use Whathood\Mapper\NeighborhoodHeatMapMapper;
use Whathood\Spatial\PHP\Types\Geometry\LineString;
use Whathood\Spatial\PHP\Types\Geometry\Polygon;
use Whathood\Spatial\PHP\Types\Geometry\Point;
use Doctrine\Common\Collections\ArrayCollection;
/**
 * We need to create a box that encoloses all neighborhood polygons for a
 * particular neighborhood
 * 
 * We need to know the total number of votes that any one point gets to be able
 * to get a proper ratio for the heat map scale.
 * 
 * @author Jim Smiley twitter:@jimRsmiley
 */
class HeatMapBuilder {
    
    protected $heatMapMapper;
    
    public function __construct( NeighborhoodHeatMapMapper $heatMapMapper ) {
        $this->heatMapMapper = $heatMapMapper;
    }
    
    /**
     * 
     * @param \Whathood\Entity\Neighborhood $testNeighborhood
     * @param \Whathood\Entity\Region $testRegion
     * @param type $sideLength
     * @return null|\Whathood\Entity\HeatMap
     * @throws \InvalidArgumentException
     */
    public function getLatestHeatMap( 
                                Neighborhood $testNeighborhood,
                                Region $testRegion, 
                                $sideLength 
    ) {
        if( empty($sideLength) )
            throw new \InvalidArgumentException('sidLength may not be null');
        
        $testNeighborhoodName = $testNeighborhood->getName();
        $testRegionName = $testRegion->getName();
        
        $neighborhoodPolygons = $this->neighborhoodPolygonMapper()
                                ->getNeighborhoodByName( 
                                        $testNeighborhoodName,$testRegionName);

        $testPoints = $this->getBoundarySquare($neighborhoodPolygons)
                                                ->getTestPoints($sideLength);
        
        $heatMapPoints = $this->getHeatMapPoints($testPoints, $testNeighborhoodName);
        
        if( count( $heatMapPoints ) > 0 ) {
            return new HeatMap( array(
                    'neighborhood'          => $testNeighborhood,
                    'region'                => $testRegion,
                    'heatMapPoints'         => $heatMapPoints,
                    'neighborhoodPolygons'  => new ArrayCollection($neighborhoodPolygons)
                ));
        } else {
            return null;
        }
    }
    
    protected function getHeatMapPoints($testPoints, $testNeighborhoodName) {
        
        $heatMapPoints = array(); 
        
        foreach( $testPoints as $point ) {
            
            // get all the neighborhood polygons that overlap that point
            $neighborhoodPolygons = $this->neighborhoodPolygonMapper()
                                        ->getNeghborhoodPolygonsByPoint($point);
            
            $consensus = new WhathoodConsensus( $neighborhoodPolygons );
            $votes = $consensus->getVoteNum( $testNeighborhoodName );
            
            if( 0 < $votes )
        
            if( $votes > 0 ) {
                
                $heatMapPoints[] = new HeatMapPoint( array(
                                        'point'  => $point,
                                        'weight' => $votes
                                    ));
            }
        }
        return $heatMapPoints;
    }
    
    /**
     * run through every neighborhood and get a square that contains
     * all of them
     */
    public function getBoundarySquare($neighborhoods) {

        if( count( $neighborhoods ) == 0 )
            throw new \InvalidArgumentException('neighborhoods may not be empty');
        
        $yMin = null;$yMax = null;$xMin = null; $xMax = null;
        
        foreach( $neighborhoods as $neighborhood ) {
            $polygon = $neighborhood->getPolygon();

            foreach( $polygon->getRings() as $ring ) {
                foreach( $ring->getHeatMapPoints() as $point ) {
                    $x = $point->getX();
                    $y = $point->getY();
                    
                    if( $yMin == null || $yMin > $point->getY() ){
                        $yMin = $y;
                    }
                    if( $yMax == null || $yMax < $point->getY() ) {
                        $yMax = $y;
                    }
                    if( $xMin == null || $xMin > $point->getX() ) {
                        $xMin = $x;
                    }
                    if( $xMax == null || $xMax < $point->getX() ) {
                        $xMax = $x;
                    }
                } // foreach point in the ring
            } // foreach ring
        } // foreach neighborhood
        
        return new BoundarySquare( array(
            'yMin' => $yMin,
            'yMax' => $yMax,
            'xMin' => $xMin,
            'xMax' => $xMax
        ));
    }
    
    public static function neighborhoodExists( $neighborhoodName, $neighborhoodHaystack ) {
        
        foreach( $neighborhoodHaystack as $n ) {
            if( $n->getName() == $neighborhoodName )
                return true;
        }
        
        return false;
    }
    
    public function neighborhoodPolygonMapper() {
        return $this->heatMapMapper->neighborhoodPolygonMapper();
    }
    
    public function neighborhoodMapper() {
        return $this->neighborhoodPolygonMapper()->neighborhoodMapper();
    }
}

?>
