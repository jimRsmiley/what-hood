<?php
namespace Whathood\Doctrine\ORM\Query;

use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query\Expr\Join;
/**
 * Description of NeighborhoodPolygonQueryBuilder
 *
 * @author Jim Smiley twitter:@jimRsmiley
 */
class HeatMapQueryBuilder extends AbstractQueryBuilder {
    
    protected $qb;
    
    public function __construct( QueryBuilder $qb ) {
        $this->qb = $qb;
        
        $this->qb->select( array( 'hm','n','r' ) )
            ->from('Whathood\Entity\HeatMap', 'hm')
            ->join('hm.region','r')
            ->join('hm.neighborhood', 'n')
            ;
    }
    

    public function setNeighborhoodName($neighborhoodName) {
        
        if( empty( $neighborhoodName ) )
            throw new \InvalidArgumentException('neighborhoodName may not be null');
        
        $this->addWhereString('n.name = :neighborhoodName');
        $this->addParameter('neighborhoodName', $neighborhoodName);
        return $this;
    }
    
    public function setRegionName($regionName) {
        
        if( empty( $regionName ) )
            throw new \InvalidArgumentException('regionName may not be null' );
        
        $this->addWhereString('r.name = :regionName');
        $this->addParameter('regionName', $regionName);

        return $this;
    }
   
    public function setNeighborhoodOlderThanHeatMap() {
        $this->qb->where( 'np.dateTimeAdded > hm.dateTimeAdded');
        return $this;
    }
    
    public function getQuery() {
        
        $this->setWhereStrings();
        $this->qb->orderBy( "hm.id",'DESC' );
        return $this->qb->getQuery();
    }
    
    public function getSql() {
        return $this->qb->getQuery()->getSql();
    }
}

?>
