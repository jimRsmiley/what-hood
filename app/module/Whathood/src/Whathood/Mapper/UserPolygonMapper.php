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
class UserPolygonMapper extends BaseMapper {
    
    public function byId( $id ) {
        
        if( empty( $id ) )
            throw new \InvalidArgumentException( 'id may not be null' );
        
        $qb = $this->em->createQueryBuilder()->select( array( 'up' ) )
            ->from('Whathood\Entity\UserPolygon', 'up')
            ->where( 'up.id = :id' )
            ->setParameter( 'id', $id );

        return $qb->getQuery()->getSingleResult();
    }
    
    public function getLatest(Neighborhood $neighborhood) {
        $dql = "SELECT np FROM NeighborhoodPolygon WHERE  ORDER BY ";
        $qb = $this->em->createQueryBuilder();
        $qb->select( array('np') )
                ->from($this->getEntityName(),'np')
                ->where( 'np.neighborhood = :neighborhood_id')
                ->orderBy( 'np.id', 'DESC' )
                ->setParameter('neighborhood_id', $neighborhood->getId())
                ->setMaxResults(1);
        
        return $qb->getQuery()->getSingleResult();
    }
    
    public function getNeighborhoodByName( $neighborhoodName, $regionName ) {
        $qb = $this->getQueryBuilder();
        $qb->setNeighborhoodName($neighborhoodName)->setRegionName($regionName);
        return $qb->getQuery()->getResult();
    }
    
    public function getNeighborhoodPolygonsByLatLng( $x, $y ) {
        //print $this->getCurrentDateTimeAsString() . " testing point\n";
        $query = $this->em->createQuery( 'SELECT up'
            . ' FROM '. $this->getEntityName(). ' up'
                . ' WHERE ST_Within(ST_SetSRID(ST_POINT(:x, :y),4326), up.polygon ) = true'
                . ' ORDER BY up.id ASC'
        );
        $query->setParameter( ':x', $x );
        $query->setParameter( ':y', $y );
        $result = $query->getResult();
        
        return $result;
    }
    
    public function getNeighborhoodPolygonsByPoint( Point $point ) {
        return $this->getNeighborhoodPolygonsByLatLng($point->getX(), $point->getY() );
    }
    
    public function byUserId( $userId ) {
        
        if( empty( $userId ) )
            throw new \InvalidArgumentException( 'userId may not be null' );
        
        $qb = new NeighborhoodPolygonQueryBuilder(
                                        $this->em->createQueryBuilder() );
        $qb->setWhathoodUserid( $userId );
            
        return $qb->getQuery()->getResult();
    }
    
    public function userPolygonGeoJsonByUserName( $whathoodUserName ) {
        
        if( empty( $whathoodUserName ) )
            throw new \InvalidArgumentException( 'userId may not be null' );
        
        $sql = "select user_polygons_geojson_by_user_id(id) as geojson FROM whathood_user WHERE user_name = :whathoodUserName";
        
        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('geojson', 'geojson');
        
        $query = $this->em->createNativeQuery($sql,$rsm);
        $query->setParameter(':whathoodUserName', $whathoodUserName );
        
        $result = $query->getSingleResult();
        
        return $result['geojson'];
    }
    
    public function save( UserPolygon $userPolygon ) {
        $whathoodUserId = $userPolygon->getWhathoodUser()->getId();
        
        if( empty( $whathoodUserId ) )
            throw new \InvalidArgumentException("cannot save a NeighborhoodPolygon with an empty whathoodUser.id");

        /*
         * REGION
         * 
         * does the region already exist, if not we need to save it in all entities
         * because it will get saved multiple times
         */
        try {
            $regionName = $userPolygon->getRegion()->getName();
            
            $region = $this->regionMapper()->getRegionByName( $regionName );
            $userPolygon->setRegion($region);
            $userPolygon->getNeighborhood()->setRegion($region);
        }
        // threw an error cause it didn't exist
        catch( \Doctrine\ORM\NoResultException $e ) {
            $region = $userPolygon->getRegion();
            $this->regionMapper()->save( $region );
            $userPolygon->setRegion($region);
            $userPolygon->getNeighborhood()->setRegion($region);
        }
        
        /*
         * NEIGHBORHOOD
         * 
         * does the neighborhood already exist, if it does reset it in the neighborhood
         */
        try {
            $neighborhoodName = $userPolygon->getNeighborhood()->getName();
            $regionName = $userPolygon->getNeighborhood()->getRegion()->getName();
            
            $neighborhood = $this->neighborhoodMapper()->getNeighborhoodByName(
                                    $neighborhoodName, $regionName );
            
            $userPolygon->setNeighborhood($neighborhood);
        }
        // threw an error cause it didn't exist, so save it
        catch( \Doctrine\ORM\NoResultException $e ) {
            $this->neighborhoodMapper->save( 
                                    $userPolygon->getNeighborhood() );
        }

        /**
         * WhathoodUser
         */
        try {
            $whathoodUser = $this->whathoodUserMapper()->byId($whathoodUserId);
            $userPolygon->setWhathoodUser($whathoodUser);
        } catch( \Doctrine\ORM\NoResultException $e ) {
            /* this should be impossible because the user id is needed
            * at the start of this function */
            throw new \Exception( "whathood user was not found with id " . $whathoodUserId . ", this is seemingingly impossible");
        }
       
        if( $userPolygon->getDateTimeAdded() == null )
            $userPolygon->setDateTimeAdded( $this->getCurrentDateTimeAsString() );
        
        if( 0 ) {
            \Zend\Debug\Debug::dump( $userPolygon->getId(), 'in UserPolygonMapper' );
            exit;
        }
        $userPolygon->setId(300);
        $this->em->persist( $userPolygon );
        $this->em->flush( $userPolygon );
    }
    
    public function update( UserPolygon $neighborhoodPolygon ) {
        $this->em->persist($neighborhoodPolygon );
        $this->em->flush( $neighborhoodPolygon );
    }
    
    public function getPolygon( $query ) {
        
        $qb = $this->em->createQueryBuilder();
        $qb->select( array('n','r','u') )
                ->from($this->getEntityName(), 'n')
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
    
    public function getPaginationQuery($x,$y) {
        $qb = $this->sm->get('mydoctrineentitymanager')
            ->createQueryBuilder()->select( array( 'up' ) )
            ->from('Whathood\Entity\UserPolygon', 'up')
            ->where('ST_Within(ST_SetSRID(ST_POINT(:x, :y),4326), up.polygon ) = true')
            ->orderBy('up.id','ASC')
            ->setParameter( 'x', $x )
            ->setParameter('y', $y );
        
        return $qb->getQuery();
    }
    public function getQueryBuilder() {
        return new NeighborhoodPolygonQueryBuilder(
                $this->em->createQueryBuilder() );
    }
    
    protected function getEntityName() {
        return 'Whathood\Entity\UserPolygon';
    }
}
?>
