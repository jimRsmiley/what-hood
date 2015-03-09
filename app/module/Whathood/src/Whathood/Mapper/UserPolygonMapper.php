<?php
namespace Whathood\Mapper;

use Doctrine\Common\Collections\ArrayCollection;

use Doctrine\ORM\Query\Expr\Join;
use CrEOF\Spatial\PHP\Types\Geometry\Point;
use Doctrine\ORM\Query\ResultSetMapping;
use Whathood\Entity\UserPolygon;
use Whathood\Entity\Neighborhood;
use Whathood\Entity\WhathoodUser;
use Whathood\Doctrine\ORM\Query\NeighborhoodPolygonQueryBuilder;
use Whathood\Model\Whathood\WhathoodResult;
/**
 * Description of NeighborhoodPolygonMapper
 *
 * @author Jim Smiley twitter:@jimRsmiley
 */
class UserPolygonMapper extends BaseMapper {

    public function fetchAll() {
        $qb = $this->em->createQueryBuilder()->select( array( 'up' ) )
            ->from('Whathood\Entity\UserPolygon', 'up')
            ->orderBy('up.id','ASC');
        return $qb->getQuery()->getResult();
    }

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

    public function getByNeighborhood( $neighborhoodName, $regionName ) {
        $dql = "SELECT up FROM Whathood\Entity\UserPolygon up
            JOIN up.neighborhood n
            JOIN n.region r
            WHERE n.name = :neighborhood
            AND r.name = :region";
        $query = $this->em->createQuery($dql)
            ->setParameter(':neighborhood',$neighborhoodName)
            ->setParameter(':region',$regionName);
        return $query->getResult();
    }

    public function getByXY($x,$y ) {
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

    public function getUserPolygonsNotAssociatedWithNeighborhoodPolygons() {
        $sql = "SELECT id FROM user_polygon WHERE id NOT IN ( SELECT up_id FROM up_np )";
        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('id','id');
        $query = $this->em->createNativeQuery($sql,$rsm);
        $result = $query->getResult();

        $user_polygons = array();
        foreach($result as $row) {
            $up = $this->byId($row['id']);
            array_push($user_polygons,$up);
        }
        return $user_polygons;
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

        $this->logger()->info("entered save");
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

        $this->logger()->info("after region");

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

        $this->logger()->info('after neighborhood');

        $whathoodUser = $this->getMaybeSaveByName($userPolygon->getWhathoodUser()->getIpAddress());
        $userPolygon->setWhathoodUser($whathoodUser);

        if( $userPolygon->getDateTimeAdded() == null )
            $userPolygon->setDateTimeAdded( $this->getCurrentDateTimeAsString() );

        if( 0 ) {
            \Zend\Debug\Debug::dump( $userPolygon->getId(), 'in UserPolygonMapper' );
            exit;
        }

        $this->logger()->info("about to save");
        $this->em->persist( $userPolygon );
        $this->logger()->info("about to flush");
        $this->em->flush( $userPolygon );
        $this->logger()->info("saved");
    }

    public function getMaybeSaveByName($ip_address) {
        if (empty($ip_address))
            throw new \InvalidArgumentException("ip_address may not be null");
        try {
            $whathood_user = $this->whathoodUserMapper()->byIpAddress($ip_address);
        } catch( \Doctrine\ORM\NoResultException $e ) {
            $whathood_user = new WhathoodUser(array(
                'ip_address' => $ip_address ));
        }
        return $whathood_user;
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

    public function getPaginationQuery(array $opts = null) {
		if (empty($opts)) $opts = array();
        $qb = $this->sm->get('mydoctrineentitymanager')
            ->createQueryBuilder()->select( array( 'up' ) )
            ->from('Whathood\Entity\UserPolygon', 'up')
            ->orderBy('up.id','ASC');

		if( isset($opts['x']) && isset($opts['y']) ) {
            $qb->where('ST_Within(ST_SetSRID(ST_POINT(:x, :y),4326), up.polygon ) = true')
				->setParameter( 'x', $opts['x'] )
				->setParameter('y', $opts['y'] );
		}

		if (isset($opts['neighborhood_id'])) {
			$qb->where('up.neighborhood = :neighborhood_id')
				->setParameter('neighborhood_id',(int)$opts['neighborhood_id']);
		}

		return $qb->getQuery();
    }
    public function getQueryBuilder() {
        return new NeighborhoodPolygonQueryBuilder(
                $this->em->createQueryBuilder() );
    }

    protected function getEntityName() {
        return 'Whathood\Entity\UserPolygon';
    }

    public function getWhathoodResult($x,$y) {
        $neighborhoods = $this->getByXY($x,$y);
        $whathoodResult = new WhathoodResult();
        $whathoodResult->setLatLng($x,$y);
        $whathoodResult->setNeighborhoods($neighborhoods);
        return $whathoodResult;
    }
}
?>
