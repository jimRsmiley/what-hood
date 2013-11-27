<?php

namespace Application\Mapper;

use CrEOF\Spatial\PHP\Types\Geometry\Point;
use Application\Entity\Neighborhood as NeighborhoodEntity;
use Application\Spatial\PHP\Types\Geometry\NeighborhoodCollection;
use Application\Doctrine\ORM\Query\NeighborhoodQueryBuilder;

/**
 * Description of NeighborhoodMapper
 *
 * @author Jim Smiley twitter:@jimRsmiley
 */
class NeighborhoodMapper extends BaseMapper {

    public function getByRegionId( $regionId ) {
        
        if( empty( $regionId ) ) {
            throw new \InvalidArgumentException("region may not be null");
        }
        
        $dql = "SELECT n FROM Application\Entity\Neighborhood  n JOIN n.region r WHERE r.id = $regionId AND n.deleted = 0";
        
        return $this->em->createQuery($dql)->getResult();
    }
    
    /**
     * return a list 
     * @param type $regionName
     * @return type
     * @throws \InvalidArgumentException
     */
    public function byRegionName( $regionName ) {
        
        if( empty( $regionName ) )
            throw new \InvalidArgumentException('regionName may not be null');
        
        $qb = new NeighborhoodQueryBuilder( $this->em->createQueryBuilder() );
        $qb->setRegionName( $regionName );
        
        return $qb->getQuery()->getResult();
    }
    
    public function nameLike( $name ) {
        
        if( empty( $name ) ) {
            throw new \InvalidArgumentException("neighborhoodName may not be null");
        }
        
        $qb = $this->em->createQueryBuilder();
        
        $qb->select('n')
                ->from('Application\Entity\Neighborhood','n')
                ->where( $qb->expr()->like('LOWER(n.name)', 'LOWER(:name)' ) )
                ->setParameter('name', '%'.$name.'%' );
        
        return $qb->getQuery()->getResult();
    }
    
    public function byId( $id ) {
        
        if( empty( $id ) )
            throw new \InvalidArgumentException( 'id may not be null' );
        
        $qb = $this->em->createQueryBuilder();
        $qb->select( array( 'n','r','u' ) )
                ->from('Application\Entity\Neighborhood', 'n')
                ->innerjoin( 'n.region','r')
                ->innerjoin( 'n.whathoodUser','u')
                ->where( 'n.id = ?1' )
                ->setParameter(1, $id );
        
        return $qb->getQuery()->getSingleResult();
    }

   public function byNeighborhoodName( $neighborhoodName, $regionName ) {
        
        $dql = "SELECT n"
                . " FROM Application\Entity\Neighborhood n"
                . " JOIN n.region r"
                . " WHERE n.name = ?1"
                . ' AND r.name = ?2';
        
        $query = $this->em->createQuery($dql)
                ->setParameter( 1, $neighborhoodName );
        
        if( !empty($regionName) )
            $query->setParameter(2, $regionName );
        
        return $query->getSingleResult();
    }

    
    public function save( NeighborhoodEntity $neighborhood ) {

        if( $neighborhood->getName() == null )
            throw new \InvalidArgumentException("neighborhood.name may" 
                                                            . " not be null");
        
        /*
         * does the region already exist, if it does reset it in the neighborhood
         */
        try {
            $region = $this->regionMapper()
                            ->byName($neighborhood->getRegion()->getName() );
            $neighborhood->setRegion($region);
        }
        // guess there was no region by that name            
        catch( \Doctrine\ORM\NoResultException $e ) {}
       
        if( $neighborhood->getDateTimeAdded() == null ) {
            $neighborhood->setDateTimeAdded( date("Y-m-d H:i:s") );
        }
        
        $this->em->persist( $neighborhood );
        $this->em->flush( $neighborhood );
    }
    

    
    /*
     * fetch all neighborhoods, ALL of them
     */
    public function fetchAll() {
        
        $qb = $this->getQueryBuilder();
        $qb->orderBy('n.name','ASC');
        
        $neighborhoods = $qb->getQuery()->getResult();
        
        return $neighborhoods;
    }

    public function getQueryBuilder() {
        return new \Application\Doctrine\ORM\Query\NeighborhoodQueryBuilder(
                $this->em->createQueryBuilder() );
    }
}

?>
