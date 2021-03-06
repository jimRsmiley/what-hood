<?php
namespace Whathood\Mapper;

use Doctrine\Common\Collections\ArrayCollection;

use Whathood\Entity\NeighborhoodBoundary;
use Whathood\Entity\Neighborhood;
use Doctrine\ORM\Query\Expr\Join;
use Whathood\Doctrine\ORM\Query\NeighborhoodBoundaryQueryBuilder;
use CrEOF\Spatial\PHP\Types\Geometry\Point;
use Whathood\Entity\Region;
use Doctrine\ORM\Query\ResultSetMapping;

class NeighborhoodBoundaryMapper extends BaseMapper {

    public function byId($neighborhoodPolygonId) {

        $query = $this->em->createQuery( 'SELECT np'
            . ' FROM '. $this->getEntityName(). ' np'
                . ' WHERE np.id = :id'
        );
        $query->setParameter( ':id', $neighborhoodPolygonId );
        $result = $query->getSingleResult();

        return $result;
    }

    /**
     * return the latest neighborhood polygon by neighborhood id
     *
     * @return mixed - a NeighborhoodBoundary entity
     */
    public function latestByNeighborhood(Neighborhood $neighborhood) {
        $query = $this->em->createQuery( 'SELECT np'
                . ' FROM '. $this->getEntityName(). ' np'
                . ' WHERE np.neighborhood = :neighborhood_id'
                . ' ORDER BY np.id DESC'
            );
        $query->setMaxResults(1);
        $query->setParameter( ':neighborhood_id', $neighborhood->getId() );
        return $query->getSingleResult();
    }

    /**
     * @description returns all the NeighborhoodBoundary objects associated with the given neighborhood
     *
     * @return array - an array of NeighborhoodBoundary entities
     */
    public function byNeighborhood(Neighborhood $neighborhood) {
        $dql = "SELECT np FROM Whathood\Entity\NeighborhoodBoundary np
            WHERE np.neighborhood = :neighborhood";
        $query = $this->em->createQuery($dql)
            ->setParameter(':neighborhood',$neighborhood->getId());
        return $query->getResult();
    }

    /**
     * pull the neighborhood boundary as geojson
     * 
     * first trying to get it from the cache, then going into the database
     * @return String the geojson
     */
    public function getNeighborhoodBoundarysAsGeoJsonByRegion(Region $region) {

        if( empty( $region->getId() ) )
            throw new \InvalidArgumentException("region.id must not be null");

        $key = "np_region_geojson-".$region->getId();

        $geojson = $this->cache()->getItem($key, $success);

        if (!$success) {
            $geojson = $this->getBoundaryAsGeoJsonFromDb($region);
            $this->logger()->info("data was pulled from db; saving in cache w key $key");
            $this->cache()->setItem($key, $geojson);
        }
        else {
            $this->logger()->info("found key $key in cache");
        }

        return $geojson;
    }

    private function getBoundaryAsGeoJsonFromDb(Region $region) {
        $sql = "SELECT latest_neighborhoods_geojson(:regionId) as geojson";

        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('geojson', 'geojson');

        $query = $this->em->createNativeQuery($sql,$rsm);
        $query->setParameter('regionId', $region->getId() );

        $result = $query->getSingleResult();

        $geojson = $result['geojson'];
        if (preg_match('/"features":null/',$geojson))
            throw new \Exception("no neighborhood polygons returned for region '".$region->getName()."'");
        return $geojson;
    }

    public function save( NeighborhoodBoundary $np ) {

        // save the neighborhood polygon
        $this->em->persist( $np );
        $this->em->flush( $np );
        $np_id = $np->getId();

        // now update the up_np table foe every UserPolygon associated with this NeighborhoodBoundary
        foreach($np->getUserPolygons() as $up) {
            $sql = "INSERT INTO up_np(up_id,np_id) VALUES (?,?)";
            $this->em->getConnection()->prepare($sql)->execute(array($up->getId(),$np_id));
        }
    }

    public function fetchAll() {
        $qb = $this->em->createQueryBuilder()->select( array( 'np' ) )
            ->from('Whathood\Entity\NeighborhoodBoundary', 'np')
            ->orderBy('np.id','ASC');
        return $qb->getQuery()->getResult();
    }

    protected function getEntityName() {
        return 'Whathood\Entity\NeighborhoodBoundary';
    }

    public function area(NeighborhoodBoundary $np) {
        $sql = "SELECT ST_Area(polygon) AS area FROM neighborhood_polygon np WHERE np.id = :np_id";

        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('area','area');
        $query = $this->em->createNativeQuery($sql,$rsm);
        $result = $query->setParameter('np_id',$np->getId())
            ->getSingleResult();
        return $result['area'];
    }
}

?>
