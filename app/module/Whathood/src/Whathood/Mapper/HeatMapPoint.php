<?php

namespace Whathood\Mapper;

use Whathood\Entity\Neighborhood;
use Whathood\Entity\HeatMapPoint as HMP;

/**
 * Description of NeighborhoodMapper
 *
 * @author Jim Smiley twitter:@jimRsmiley
 */
class HeatMapPoint extends BaseMapper {

    public function byNeighborhood( Neighbborhood $neighborhood ) {

        if( empty( $neighborhood ) )
            throw new \InvalidArgumentException( ' may not be null' );

        $qb = $this->em->createQueryBuilder();
        $qb->select( array( 'n','r' ) )
                ->from('Whathood\Entity\HeatMapPoint', 'hmp')
                ->where( 'hmp.neighborhood = ?1' )
                ->setParameter(1, $neighborhood->getId() );

        try {
            return $qb->getQuery()->getResult();
        } catch( \Exception $e ) {
            print $e->getMessage();
            exit;
        }
    }

    public function savePoints(array $heatmap_points) {
        foreach($heatmap_points as $hmp) {
            $this->save($hmp);
        }
    }

    public function save( HMP $heatmap_point ) {
        $this->em->persist( $heatmap_point );
        $this->em->flush( $heatmap_point );
    }

    public function getQueryBuilder() {
        return new \Whathood\Doctrine\ORM\Query\NeighborhoodQueryBuilder(
                $this->em->createQueryBuilder() );
    }
}
?>
