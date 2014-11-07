<?php
namespace Whathood\Mapper;

use Whathood\Entity\Region as RegionEntity;
use Doctrine\ORM\Query\ResultSetMapping;

/**
 * Description of RegionMapper
 *
 * @author Jim Smiley twitter:@jimRsmiley
 */
class RegionMapper extends BaseMapper {
    
    public function getRegionByName( $name ) {
        
        if( empty( $name ) ) {
            throw new \InvalidArgumentException("regionName may not be null");
        }
        
        $qb = $this->em->createQueryBuilder();
        
        $qb->select('r')
                ->from('Whathood\Entity\Region','r')
                ->where( $qb->expr()->eq('r.name', ':name' ) )
                ->setParameter('name', $name );
        $region = $qb->getQuery()->getSingleResult();
        
        return $region;
    }
    
    public function byId( $id ) {
        
        if( empty( $id ) ) {
            throw new \InvalidArgumentException("regionName may not be null");
        }
        
        $qb = $this->em->createQueryBuilder();
        
        $qb->select('r')
                ->from('Whathood\Entity\Region','r')
                ->where( $qb->expr()->eq('r.id', ':id' ) )
                ->setParameter('id', $id );
        $region = $qb->getQuery()->getSingleResult();
        
        return $region;
    }

    public function nameLike( $name ) {
        
        if( empty( $name ) ) {
            throw new \InvalidArgumentException("regionName may not be null");
        }
        
        $qb = $this->em->createQueryBuilder();
        
        $qb->select('r')
                ->from('Whathood\Entity\Region','r')
                ->where( $qb->expr()->like('LOWER(r.name)', 'LOWER(:name)' ) )
                ->setParameter('name', '%'.$name.'%' );
        
        return $qb->getQuery()->getResult();
    }
    
    public function fetchDistinctRegionNames() {
        $query = $this->em->createQuery( 'SELECT region.name'
                . ' FROM Whathood\Entity\Region region'
                . ' GROUP BY region.name' 
        );
        $regions = $query->getResult();
        
        $regionNames = array();
        foreach( $regions as $info ) {
            $regionNames[] = $info['name'];
        }
        
        return $regionNames;
    }
    
    public function pointIsInRegion( $regionId, $point ) {
        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('result','result');
        $sql = "SELECT ST_Contains( polygon, ST_SetSRID(ST_MakePoint( :x, :y ),4326) ) as result FROM region WHERE id = :regionId";
        $query = $this->em->createNativeQuery( $sql, $rsm );
        $query->setParameter( ':x', $point->getX() );
        $query->setParameter( ':y', $point->getY() );
        $query->setParameter( ':regionId', $regionId );
        $result = $query->getSingleResult();
        
        return $result['result'];
    }
    
    public function fetchAll() {
        $regions = $this->em->getRepository( 'Whathood\Entity\Region' )
                ->findAll();
        return $regions;
    }
    
    public function save( RegionEntity $region ) {
        $this->em->persist( $region );
        $this->em->flush( $region );
    }

    public function merge( RegionEntity $region ) {
        $this->em->merge( $region );
        $this->em->flush( $region );
    }

    
    public function getQueryBuilder() {
        throw new \Exception("not yet implmeneted");
    }
}

?>
