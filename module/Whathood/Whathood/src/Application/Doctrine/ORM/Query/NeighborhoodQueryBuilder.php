<?php
namespace Application\Doctrine\ORM\Query;

use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query\Expr\Join;
/**
 * Description of NeighborhoodPolygonQueryBuilder
 *
 * @author Jim Smiley twitter:@jimRsmiley
 */
class NeighborhoodQueryBuilder extends AbstractQueryBuilder {
    
    public function __construct( QueryBuilder $qb ) {
        $this->qb = $qb;
        
        $this->qb->select( array( 'n' ) )
            ->from('Application\Entity\Neighborhood', 'n')
            ->join('n.region','r')
            ;
    }
    
    public function setRegionName($regionName) {
        $this->qb->where('r.name = :regionName')
                ->setParameter( 'regionName', $regionName );
        return $this;
    }
    
    public function likeNeighborhoodname($neighborhoodName) {
        $this->qb->where('n.name LIKE %:neighborhoodName%')
                ->setParameter( 'neighborhoodName', $regionName );
        return $this;
    }
    
    public function orderBy( $str1, $str2 ) {
        $this->qb->orderBy( $str1, $str2 );
    }
    
    public function getQuery() {
        return $this->qb->getQuery();
    }
}

?>
