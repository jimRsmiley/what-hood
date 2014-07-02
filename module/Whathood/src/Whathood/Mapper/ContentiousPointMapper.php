<?php
namespace Whathood\Mapper;

use Doctrine\Common\Collections\ArrayCollection;

use Whathood\Entity\UserPolygon;
use Whathood\Entity\Neighborhood;
use Doctrine\ORM\Query\Expr\Join;
use Whathood\Doctrine\ORM\Query\NeighborhoodPolygonQueryBuilder;
use CrEOF\Spatial\PHP\Types\Geometry\Point;
use Doctrine\ORM\Query\ResultSetMapping;
/**
 * Description of NeighborhoodPolygonMapper
 *
 * @author Jim Smiley twitter:@jimRsmiley
 */
class ContentiousPointMapper extends BaseMapper {
    
    /**
    *
    *   get contentious points by the create event
    *
    **/
    public function contentiousPointsByCreateEventId( $createEventId ) {
        
        if( empty( $createEventId ) ) {
            throw new \InvalidArgumentException('createEventId may not be empty');
        }
        $sql = "SELECT ST_X(point) as x, ST_Y(point) as y FROM contentious_point WHERE create_event_id = :createEventId";
        
        $rsm = new \Doctrine\ORM\Query\ResultSetMapping();
        $rsm->addScalarResult('x','x');
        $rsm->addScalarResult('y','y');
        
        $query = $this->em->createNativeQuery( $sql, $rsm );
        $query->setParameter( 'createEventId', $createEventId );
        return $query->getResult();
        
    }
    
    public function pointsToHeatmapJsData( $points ) {
        
        $heatmapJsData = new \Whathood\HeatmapJsData( array('maxValue'=>1) );
        foreach( $points as $point ) {
            $heatmapJsData->addPoint( $point['x'], $point['y'], $value = 1 );
        }
        
        return $heatmapJsData;
    }
}
?>
