<?php
namespace Whathood\Mapper;

use Doctrine\Common\Collections\ArrayCollection;

use Whathood\Entity\NeighborhoodPolygon;
use Whathood\Entity\Neighborhood;
use Doctrine\ORM\Query\Expr\Join;
use Whathood\Doctrine\ORM\Query\NeighborhoodPolygonQueryBuilder;
use CrEOF\Spatial\PHP\Types\Geometry\Point;
use Whathood\Entity\Region;
use Doctrine\ORM\Query\ResultSetMapping;

class NeighborhoodPolygonMapper extends BaseMapper {

    public function byId($neighborhoodPolygonId) {

        //print $this->getCurrentDateTimeAsString() . " testing point\n";
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
     * @return mixed - a NeighborhoodPolygon entity
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
     * @description returns all the NeighborhoodPolygon objects associated with the given neighborhood
     *
     * @return array - an array of NeighborhoodPolygon entities
     */
    public function byNeighborhood(Neighborhood $neighborhood) {
        $dql = "SELECT np FROM Whathood\Entity\NeighborhoodPolygon np
            WHERE np.neighborhood = :neighborhood";
        $query = $this->em->createQuery($dql)
            ->setParameter(':neighborhood',$neighborhood->getId());
        return $query->getResult();
    }

    public function getNeighborhoodPolygonsAsGeoJsonByRegion(Region $region) {
        if( empty( $region->getId() ) )
            throw new \InvalidArgumentException("region.id must not be null");

        $sql = "SELECT whathood.latest_neighborhoods_geojson(:regionId) as geojson";

        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('geojson', 'geojson');

        $query = $this->em->createNativeQuery($sql,$rsm);
        $query->setParameter('regionId', $region->getId() );

        $result = $query->getSingleResult();

        $geojson = $result['geojson'];
        if (preg_match('/"features":null/',$geojson))
            throw new \Exception("no neighborhood polygons returned for region '".$region->getName()."'");
        else
            return $geojson;
    }

    /**
     *  return a geosjon representation of a neighborhood border given user polygons
     *  
     *  @return string geojson of a neighborhood border
     **/
    public function generateBorder(
        \Doctrine\ORM\PersistentCollection $user_polygons,
        $neighborhood_id,
        $grid_resolution,
        $target_percentage
    ) {
        $up_ids = array();
        foreach($user_polygons as $up) $up_ids[] = $up->getId();
        $up_id_str = join(',',$up_ids);

        $sql = "SELECT
            ST_AsGeoJSON(
                ST_ConcaveHull(
                    whathood.neighborhood_point_geometry(:neighborhood_id,ST_Collect(up.polygon),:grid_resolution),
                    $target_percentage
                )
            ) as geojson
            FROM user_polygon up
            WHERE up.id IN ( $up_id_str )";

        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('geojson','geojson');
        $query = $this->em->createNativeQuery($sql,$rsm);
        $query->setParameter('neighborhood_id',$neighborhood_id);
        $query->setParameter('grid_resolution',$grid_resolution);

        $result = $query->getSingleResult();
        return $result['geojson'];
    }

    public function save( NeighborhoodPolygon $np ) {

        // save the neighborhood polygon
        $this->em->persist( $np );
        $this->em->flush( $np );
        $np_id = $np->getId();

        // now update the up_np table foe every UserPolygon associated with this NeighborhoodPolygon
        foreach($np->getUserPolygons() as $up) {
            $sql = "INSERT INTO up_np(up_id,np_id) VALUES (?,?)";
            $this->em->getConnection()->prepare($sql)->execute(array($up->getId(),$np_id));
        }
    }

    public function fetchAll() {
        $qb = $this->em->createQueryBuilder()->select( array( 'np' ) )
            ->from('Whathood\Entity\NeighborhoodPolygon', 'np')
            ->orderBy('np.id','ASC');
        return $qb->getQuery()->getResult();
    }

    protected function getEntityName() {
        return 'Whathood\Entity\NeighborhoodPolygon';
    }
}

?>
