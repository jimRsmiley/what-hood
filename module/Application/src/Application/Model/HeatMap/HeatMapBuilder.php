<?php //
namespace Application\Model\HeatMap;

use Application\Entity\Neighborhood;
use Application\Entity\HeatMap;
use Application\Entity\Region;
use Application\Model\Whathood\WhathoodConsensus;
use Application\Model\HeatMap\Point as HeatMapPoint;
use Application\Mapper\HeatMapMapper;
use Application\Spatial\PHP\Types\Geometry\LineString;
use Application\Spatial\PHP\Types\Geometry\Polygon;
use Application\Spatial\PHP\Types\Geometry\Point;
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
    
    public function __construct( HeatMapMapper $heatMapMapper ) {
        $this->heatMapMapper = $heatMapMapper;
    }
    
    /**
     * 
     * @param \Application\Entity\Neighborhood $testNeighborhood
     * @param \Application\Entity\Region $testRegion
     * @param type $sideLength
     * @return null|\Application\Entity\HeatMap
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
                                ->byNeighborhoodName( 
                                        $testNeighborhoodName,$testRegionName);

        if( empty( $neighborhoodPolygons ) )
            throw new \Exception("neighborhoodPolygons cannot be empty");
        
        
        $testPoints = $this->getBoundarySquare($neighborhoodPolygons)
                                                ->getTestPoints($sideLength);
        
        $heatMapPoints = array(); 
        $maxValueCalculator = new MaxCalculator();
        $counter = 1;
        
        $npCollection = new ArrayCollection($neighborhoodPolygons);
        
        /*
         * for each test point in the boundary square
         */
        foreach( $testPoints as $point ) {
            
            // get all the neighborhood polygons that overlap that point
            $neighborhoodPolygons = $this->neighborhoodPolygonMapper()
                                                            ->byPoint($point);
            
            
            $consensus = new WhathoodConsensus( $neighborhoodPolygons );
            $votes = $consensus->getVoteNum( $testNeighborhoodName );
            
            if( 0 < $votes )
                $maxValueCalculator->add( $consensus->getTotalVotes() );
        
            if( $votes > 0 ) {
                $heatMapPoints[] = new HeatMapPoint(
                                        $point->getX(),
                                        $point->getY(),
                                        $votes
                                    );
            }
        }
        
        if( count( $heatMapPoints ) > 0 ) {
            return new HeatMap( array(
                    'neighborhood'  => $testNeighborhood,
                    'region'            => $testRegion,
                    'points'            => $heatMapPoints,
                    'max'               => $maxValueCalculator->getMaxValue(),
                    'neighborhoodPolygons' => $npCollection
                ));
        } else {
            return null;
        }
    }
    
    /**
     * run through every neighborhood and get a square that contains
     * all of them
     */
    public function getBoundarySquare($neighborhoods) {

        if( count( $neighborhoods ) == 0 )
            throw new \InvalidArgumentException('neighborhoods may not be empty');
        
        $latMin = null;$latMax = null;$lngMin = null; $lngMax = null;
        
        foreach( $neighborhoods as $neighborhood ) {
            $polygon = $neighborhood->getPolygon();

            foreach( $polygon->getRings() as $ring ) {
                foreach( $ring->getPoints() as $point ) {
                    $x = $point->getX();
                    $y = $point->getY();
                    
                    if( $latMin == null || $latMin > $point->getX() ){
                        $latMin = $x;
                    }
                    if( $latMax == null || $latMax < $point->getX() ) {
                        $latMax = $x;
                    }
                    if( $lngMin == null || $lngMin > $point->getY() ) {
                        $lngMin = $y;
                    }
                    if( $lngMax == null || $lngMax < $point->getY() ) {
                        $lngMax = $y;
                    }
                } // foreach point in the ring
            } // foreach ring
        } // foreach neighborhood

        if( empty( $latMin ) ) {
            die( 'it should be impossible to have an empty latMin here' );
        }
        
        return new BoundarySquare( array(
            'latMin' => $latMin,
            'latMax' => $latMax,
            'lngMin' => $lngMin,
            'lngMax' => $lngMax
        ));
    }
    
    public static function neighborhoodExists( $neighborhoodName, $haystack ) {
        
        foreach( $haystack as $n ) {
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
