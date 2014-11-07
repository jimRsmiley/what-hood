<?php
namespace Whathood\Model;

use Whathood\Entity\NeighborhoodHeatMapPoint;
/**
 * Description of NeighborhoodHeatMapPointBuilder
 *
 * @author Jim Smiley twitter:@jimRsmiley
 */
class NeighborhoodHeatMapPointBuilder {
    
    public static function createNeighborhoodHeatMapPoint( $setNumber, $neighborhoodMapper, $heatMapTestPoint, $neighborhoodPolygons ) {
        
        $neighborhoodHeatMapPoints = array();
        $neighborhoodNameCount = self::getNeighborhoodNameCount( $neighborhoodPolygons );
        $totalNeighborhoodPolygons = count( $neighborhoodPolygons );
        
        foreach( $neighborhoodNameCount as $neighborhoodId => $count ) {
            $weight = $count / $totalNeighborhoodPolygons;
            $neighborhood = $neighborhoodMapper->getNeighborhoodById( $neighborhoodId );

            $neighborhoodHeatMapPoints[] = new NeighborhoodHeatMapPoint( array(
                'setNumber' => $setNumber,
                'neighborhood' => $neighborhood,
                'point' => $heatMapTestPoint->getPoint(),
                'strengthOfIdentity'   => $weight,
                'neighborhoodPolygons' => $neighborhoodPolygons
            ));
        }
            
        return $neighborhoodHeatMapPoints;
    }
    
    public static function getNeighborhoodNameCount( $neighborhoodPolygons ) {
        
        $neighborhoodNameCount = array();
        foreach( $neighborhoodPolygons as $neighborhoodPolygon ) {
            $neighborhoodId = $neighborhoodPolygon->getNeighborhood()->getId();
            if( !array_key_exists($neighborhoodId, $neighborhoodNameCount ) )
                    $neighborhoodNameCount[$neighborhoodId] = 0;
            
            $neighborhoodNameCount[$neighborhoodId]++;
        }
        
        return $neighborhoodNameCount;
    }
}

?>
