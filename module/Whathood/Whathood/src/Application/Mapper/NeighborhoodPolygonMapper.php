<?php
namespace Application\Mapper;

use Doctrine\Common\Collections\ArrayCollection;

use Application\Entity\NeighborhoodPolygon;
use Application\Entity\Neighborhood;
use Doctrine\ORM\Query\Expr\Join;
use Application\Doctrine\ORM\Query\NeighborhoodPolygonQueryBuilder;
use Application\Spatial\PHP\Types\Geometry\Point;
/**
 * Description of NeighborhoodPolygonMapper
 *
 * @author Jim Smiley twitter:@jimRsmiley
 */
class NeighborhoodPolygonMapper extends BaseMapper {
    
    public function byId( $id ) {
        
        if( empty( $id ) )
            throw new \InvalidArgumentException( 'id may not be null' );
        
        $qb = new NeighborhoodPolygonQueryBuilder( 
                                            $this->em->createQueryBuilder() );
        $qb->setNeighborhoodPolygonId( $id );

        return $qb->getQuery()->getSingleResult();
    }
    
    public function getLatest(Neighborhood $neighborhood) {
        $dql = "SELECT np FROM NeighborhoodPolygon WHERE  ORDER BY ";
        $qb = $this->em->createQueryBuilder();
        $qb->select( array('np') )
                ->from('Application\Entity\NeighborhoodPolygon','np')
                ->where( 'np.neighborhood = :neighborhood_id')
                ->orderBy( 'np.id', 'DESC' )
                ->setParameter('neighborhood_id', $neighborhood->getId())
                ->setMaxResults(1);
        
        return $qb->getQuery()->getSingleResult();
    }
    
    public function getAuthoritativeNeghborhoodsByRegionName( $regionName ) {
        
        if( empty( $regionName ) ) {
            throw new \InvalidArgumentException("regionName may not be null");
        }
        
        $qb = new NeighborhoodPolygonQueryBuilder( 
                                            $this->em->createQueryBuilder() );
        $qb->setRegionName( $regionName );
        
        return $qb->getQuery()->getResult();
    }
    
    public function fetchAll() {
        
        $qb = $this->em->createQueryBuilder();
        $qb->select( array('np') )
                ->from('Application\Entity\NeighborhoodPolygon', 'np' )
                ->join('np.whathoodUser', 'whu')
                ->join('np.neighborhood', 'n')
                ;
        return $qb->getQuery()->getResult();
    }
    
    public function votesById( $neighborhoodId ) {
        
        $qb = $this->em->createQueryBuilder();
        $qb->select( array('nv') )
                ->from('Application\Entity\NeighborhoodPolygonVote', 'nv' )
                ->innerJoin( 'Application\Entity\Neighborhood','n')
                ->where( $qb->expr()->eq('n.id', $neighborhoodId ) );
        
        return $qb->getQuery()->getResult();
    }
    
    public function byNeighborhoodName( $neighborhoodName, $regionName ) {
        $qb = $this->getQueryBuilder();
        $qb->setNeighborhoodName($neighborhoodName)->setRegionName($regionName);
        return $qb->getQuery()->getResult();
    }
    
    public function byLatLng( $lat, $lng ) {
        $query = $this->em->createQuery( 'SELECT n'
            . ' FROM Application\Entity\NeighborhoodPolygon n'
                . ' WHERE n.deleted = false AND ST_Within(ST_POINT(:lat, :lng), n.polygon) = true' 
        );
        $query->setParameter( ':lat', $lat );
        $query->setParameter( ':lng', $lng );

        return $query->getResult();
    }
    
    public function byPoint( Point $point ) {
        return $this->byLatLng($point->getX(), $point->getY() );
    }
    
    public function byUserId( $userId ) {
        
        if( empty( $userId ) )
            throw new \InvalidArgumentException( 'userId may not be null' );
        
        $qb = new NeighborhoodPolygonQueryBuilder(
                                        $this->em->createQueryBuilder() );
        $qb->setWhathoodUserid( $userId );
            
        return $qb->getQuery()->getResult();
    }
    
    public function byUserName( $userName ) {
        
        if( empty( $userName ) )
            throw new \InvalidArgumentException( 'userId may not be null' );
        
        $qb = new NeighborhoodPolygonQueryBuilder(
                                        $this->em->createQueryBuilder() );
        $qb->setWhathoodUserName( $userName );
            
        return $qb->getQuery()->getResult();
    }
    
    public function save( NeighborhoodPolygon $neighborhoodPolygon ) {
        $whathoodUserId = $neighborhoodPolygon->getWhathoodUser()->getId();
        
        if( empty( $whathoodUserId ) )
            throw new \InvalidArgumentException("cannot save a NeighborhoodPolygon with an empty whathoodUser.id");

        /*
         * REGION
         * 
         * does the region already exist, if not we need to save it in all entities
         * because it will get saved multiple times
         */
        try {
            $regionName = $neighborhoodPolygon->getRegion()->getName();
            
            $region = $this->regionMapper()->byName( $regionName );
            $neighborhoodPolygon->setRegion($region);
            $neighborhoodPolygon->getNeighborhood()->setRegion($region);
        }
        // threw an error cause it didn't exist
        catch( \Doctrine\ORM\NoResultException $e ) {
            $region = $neighborhoodPolygon->getRegion();
            $this->regionMapper()->save( $region );
            $neighborhoodPolygon->setRegion($region);
            $neighborhoodPolygon->getNeighborhood()->setRegion($region);
        }
        
        /*
         * NEIGHBORHOOD
         * 
         * does the neighborhood already exist, if it does reset it in the neighborhood
         */
        try {
            $neighborhoodName = $neighborhoodPolygon->getNeighborhood()->getName();
            $regionName = $neighborhoodPolygon->getNeighborhood()->getRegion()->getName();
            
            $neighborhood = $this->neighborhoodMapper()->byNeighborhoodName(
                                    $neighborhoodName, $regionName );
            
            $neighborhoodPolygon->setNeighborhood($neighborhood);
        }
        // threw an error cause it didn't exist, so save it
        catch( \Doctrine\ORM\NoResultException $e ) {
            $this->neighborhoodMapper->save( 
                                    $neighborhoodPolygon->getNeighborhood() );
        }

        /**
         * WhathoodUser
         */
        try {
            $whathoodUser = $this->whathoodUserMapper()->byId($whathoodUserId);
            $neighborhoodPolygon->setWhathoodUser($whathoodUser);
        } catch( \Doctrine\ORM\NoResultException $e ) {
            /* this should be impossible because the user id is needed
            * at the start of this function */
            throw new \Exception( "whathood user was not found with id " . $whathoodUserId . ", this is seemingingly impossible");
        }
       
        if( $neighborhoodPolygon->getDateTimeAdded() == null )
            $neighborhoodPolygon->setDateTimeAdded( $this->getCurrentDateTimeAsString() );
        
        $this->em->persist( $neighborhoodPolygon );
        $this->em->flush( $neighborhoodPolygon );
    }
    
    public function update( NeighborhoodPolygon $neighborhoodPolygon ) {
        $this->em->persist($neighborhoodPolygon );
        $this->em->flush( $neighborhoodPolygon );
    }
    
    public function getPolygon( $query ) {
        
        $qb = $this->em->createQueryBuilder();
        $qb->select( array('n','r','u') )
                ->from('Application\Entity\NeighborhoodPolygon', 'n')
                ->innerjoin('n.region', 'r')
                ->innerjoin('n.whathoodUser', 'u')
                ->where( 'n.deleted = 0');
        
        if( array_key_exists('lat',$query) && array_key_exists('lng',$query) ) {
            $qb->andWhere("ST_WITHIN( POINT( :lat, :lng), n.polygon) = 1")
                    ->setParameter('lat', $query['lat'] )
                    ->setParameter('lng', $query['lng'] );
        }
        
        return $qb->getQuery()->getResult();
    }
    
    public function getQueryBuilder() {
        return new NeighborhoodPolygonQueryBuilder(
                $this->em->createQueryBuilder() );
    }
}

?>
