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
    
    public function getNeighborhoodPolygonsAsGeoJsonByRegion( Region $region, $createEventId ) {
        
        if( empty( $region->getId() ) )
            throw new \InvalidArgumentException("region.id must not be null");
        
        $sql = "SELECT whathood.neighborhoods_geojson(:createEventId,:regionId) as geojson";
        
        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('geojson', 'geojson');
        
        $query = $this->em->createNativeQuery($sql,$rsm);
        $query->setParameter('createEventId',$createEventId);
        $query->setParameter('regionId', $region->getId() );
        
        $result = $query->getSingleResult();
        
        return $result['geojson'];
    }
    
public function getLocationCountsByNeighborhoodAsGeoJson() {
        
        $sql = "SELECT row_to_json( fc ) as geojson
FROM ( SELECT 'FeatureCollection' as type, array_to_json(array_agg(f)) as features
FROM( SELECT 'Feature' as type
    , ST_AsGeoJSON( neighborhood_location_count.neighborhood_polygon)::json AS geometry
    , row_to_json( (SELECT l FROM ( SELECT neighborhood_name,y2007,y2008,y2009,y2010,y2011,y2012,y2013, (cast(y2007 as float)+y2008+y2009+y2010+y2011)/5 as avg_2007_to_2011, (cast(y2012 as float)+y2013)/2 as avg_2012_and_2013, ( ( (cast(y2012 as float)+y2013)/2) - ( ( (cast(y2007 as float)+y2008+y2009+y2010+y2011)/5) ) ) / ( ( cast(y2007 as float)+y2008+y2009+y2010+y2011)/5 )*100 as gentrifyer  ) AS l
    )) AS properties
FROM neighborhood_location_count ) as f ) as fc
";
        $rsm = new \Doctrine\ORM\Query\ResultSetMapping();
        $rsm->addScalarResult('geojson', 'geojson');
        $query = $this->em->createNativeQuery( $sql, $rsm );
        $result = $query->getSingleResult();
        return $result['geojson'];
    }
    
    public function save( NeighborhoodPolygon $neighborhoodPolygon ) {
        $this->em->persist( $neighborhoodPolygon );
        $this->em->flush( $neighborhoodPolygon );
    }
    
    public function deleteNeighborhoodPolygonsBySetNumber( $setNumber ) {
        $qb = $this->em->createQueryBuilder();
        $qb->delete()
                ->from( $this->getEntityName(), 'np' )
                ->where( 'np.setNumber = :setNumber')
                ->setParameter( 'setNumber', $setNumber );
        
        $qb->getQuery()->execute();
    }
    

    
    protected function getEntityName() {
        return 'Whathood\Entity\NeighborhoodPolygon';
    }
}

?>
