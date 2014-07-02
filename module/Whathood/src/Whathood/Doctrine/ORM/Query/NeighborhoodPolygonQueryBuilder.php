<?php
namespace Application\Doctrine\ORM\Query;

use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query\Expr\Join;
/**
 * Description of NeighborhoodPolygonQueryBuilder
 *
 * @author Jim Smiley twitter:@jimRsmiley
 */
class NeighborhoodPolygonQueryBuilder extends AbstractQueryBuilder {
    
    protected $qb;
    
    protected $fetchDeletedPolygons = false;
    
    public function __construct( QueryBuilder $qb ) {
        $this->qb = $qb;
        
        $this->qb->select( array( 'np','n','r','u' ) )
            ->from('Application\Entity\NeighborhoodPolygon', 'np')
            ->join('np.neighborhood','n')
            ->join('np.whathoodUser','u')
            ->join('np.region','r')
            
            ;
    }
    
    public function setNeighborhoodPolygonId($id) {
        
        if( empty($id) )
            throw new \InvalidArgumentException( "id must not be empty" );
        
        $this->addWhereString('np.id = :neighborhoodPolygonId');
        $this->addParameter('neighborhoodPolygonId', $id);
        
        // if we're doing it by id, no need to not fetch a deleted polyogon
        $this->setFetchDeletedPolygons( false );
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
    
    public function setLatLng( $lat, $lng ) {
        $this->addWhereString("ST_Within(ST_Point(:lat,:lng), np.polygon) = true");
        $this->addParameter('lat', $lat);
        $this->addParameter('lng', $lng);
    }
    
    public function setWhathoodUserId( $userId ) {
        
        if( empty( $userId ) )
            throw new \InvalidArgumentException('userId may not be empty');
        
        $this->qb->where("u.id = '".$userId."'" );
    }
    
    public function setWhathoodUserName( $userName ) {
        
        if( empty( $userName ) )
            throw new \InvalidArgumentException('userName may not be empty');
        
        $this->qb->where("u.userName = '".$userName."'" );
    }
    
    public function setFetchDeletedPolygons($bool) {
        $this->fetchDeletedPolygons = $bool;
    }
    
    public function getQuery() {
        
        $this->setWhereStrings();
        // if we don't want to fetch deleted polygons
        if( $this->fetchDeletedPolygons == true ) {
            $this->qb->andWhere( 'np.deleted = false');
        }
        return $this->qb->getQuery();
    }
}

?>
