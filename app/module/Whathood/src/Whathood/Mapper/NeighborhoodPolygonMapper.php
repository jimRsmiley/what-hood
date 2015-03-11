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
/**
 * Description of NeighborhoodPolygonMapper
 *
 * @author Jim Smiley twitter:@jimRsmiley
 */
class NeighborhoodPolygonMapper extends BaseMapper {

    public function getNeighborhoodPolygonByNeighborhoodId($neighborhoodId) {

        //print $this->getCurrentDateTimeAsString() . " testing point\n";
        $query = $this->em->createQuery( 'SELECT np'
            . ' FROM '. $this->getEntityName(). ' n'
            . ' WHERE np.neighborhood = :neighborhoodId'
        );
        $query->setParameter( ':neighborhoodId', $neighborhoodId );
        $result = $query->getSingleResult();

        return $result;
    }

    public function latestByNeighborhoodId($n_id) {
        $query = $this->em->createQuery( 'SELECT np'
                . ' FROM '. $this->getEntityName(). ' np'
                . ' JOIN Whathood\Entity\Neighborhood n'
                . ' WHERE n.id = :id'
                . ' ORDER BY np.id DESC'
            );
        $query->setMaxResults(1);
        $query->setParameter( ':id', $n_id );
        return $query->getSingleResult();
    }

    public function getNpById($neighborhoodPolygonId) {

        //print $this->getCurrentDateTimeAsString() . " testing point\n";
        $query = $this->em->createQuery( 'SELECT np'
            . ' FROM '. $this->getEntityName(). ' np'
                . ' WHERE np.id = :id'
        );
        $query->setParameter( ':id', $neighborhoodPolygonId );
        $result = $query->getSingleResult();

        return $result;
    }

    public function getNeighborhoodPolygonsAsGeoJsonByRegion(Region $region) {
        if( empty( $region->getId() ) )
            throw new \InvalidArgumentException("region.id must not be null");

        $sql = "SELECT latest_neighborhoods_geojson(:regionId) as geojson";

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
        $this->em->persist( $np );

        $np_id = $np->getId();
        foreach($np->getUserPolygons() as $up) {
            $sql = "INSERT INTO up_np(up_id,np_id) VALUES (?,?)";
            $this->em->getConnection()->prepare($sql)->execute(array($up->getId(),$np_id));
        }
        $this->em->flush( $np );
    }

    protected function getEntityName() {
        return 'Whathood\Entity\NeighborhoodPolygon';
    }
}

?>
