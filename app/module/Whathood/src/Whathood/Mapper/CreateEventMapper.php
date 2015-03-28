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
class CreateEventMapper extends BaseMapper {
    
    /**
    *
    *   get contentious points by the create event
    *
    **/
    public function fetchAll() {
        $qb = $this->em->createQueryBuilder();
        
        $qb->select('r')
                ->from('Whathood\Entity\CreateEvent','r');

        $result = $qb->getQuery()->execute();
        return $result;
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
