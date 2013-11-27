<?php
namespace Application\Mapper;

use Application\Entity\Region as RegionEntity;
/**
 * Description of RegionMapper
 *
 * @author Jim Smiley twitter:@jimRsmiley
 */
class RegionMapper extends BaseMapper {
    
    public function byName( $name ) {
        
        if( empty( $name ) ) {
            throw new \InvalidArgumentException("regionName may not be null");
        }
        
        $qb = $this->em->createQueryBuilder();
        
        $qb->select('r')
                ->from('Application\Entity\Region','r')
                ->where( $qb->expr()->eq('r.name', ':name' ) )
                ->setParameter('name', $name );
        $region = $qb->getQuery()->getSingleResult();
        
        return $region;
    }
    
    public function nameLike( $name ) {
        
        if( empty( $name ) ) {
            throw new \InvalidArgumentException("regionName may not be null");
        }
        
        $qb = $this->em->createQueryBuilder();
        
        $qb->select('r')
                ->from('Application\Entity\Region','r')
                ->where( $qb->expr()->like('LOWER(r.name)', 'LOWER(:name)' ) )
                ->setParameter('name', '%'.$name.'%' );
        
        return $qb->getQuery()->getResult();
    }
    
    public function fetchDistinctRegionNames() {
        $query = $this->em->createQuery( 'SELECT region.name'
                . ' FROM Application\Entity\Region region'
                . ' GROUP BY region.name' 
        );
        $regions = $query->getResult();
        
        $regionNames = array();
        foreach( $regions as $info ) {
            $regionNames[] = $info['name'];
        }
        
        return $regionNames;
    }
    
    public function fetchAll() {
        $regions = $this->em->getRepository( 'Application\Entity\Region' )
                ->findAll();
        return $regions;
    }
    
    public function save( RegionEntity $region ) {
        
        $this->em->persist( $region );
        $this->em->flush( $region );
    }
    
    public function getQueryBuilder() {
        throw new \Exception("not yet implmeneted");
    }
}

?>
