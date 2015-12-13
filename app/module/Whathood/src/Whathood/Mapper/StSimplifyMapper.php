<?php

namespace Whathood\Mapper;

use Doctrine\ORM\Query\ResultSetMapping;

use Whathood\Spatial\PHP\Types\Geometry\Polygon;

class StSimplifyMapper extends BaseMapper {

    /**
     * simplify the polygon
     *
     * @param $polygon Polygon the polygon to simplify
     * @param $tolerance float the tolerance
     *
     * @return Polygon CrEOF type polygon
     */
    public function simplify(Polygon $polygon, $tolerance) {

        $sql = "SELECT ST_AsEWKB(ST_Simplify(ST_GeomFromText(:geom_text), :tolerance)) as simplified";

        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('simplified', 'simplified');

        $query = $this->em->createNativeQuery($sql, $rsm);
        $query->setParameter('geom_text', $polygon->__toString() );
        $query->setParameter('tolerance', $tolerance );
        $result = $query->getSingleResult();

        $geom_type = \Doctrine\DBAL\Types\Type::getType('polygon');
        $polygon = $geom_type->convertToPHPValue(
            $result['simplified'],
            $this->em->getConnection()->getDatabasePlatform());

        return $polygon;
    }


    public function simplify_debug(Polygon $polygon, $tolerance) {
        $dql = "select
          ST_Npoints(:geometry) As np_before,
          ST_NPoints(ST_Simplify(:geometry, :tolerence_1)) As np01_notbadcircle,
          ST_NPoints(ST_Simplify(:geometry, 0.80)) As np05_notquitecircle,
          ST_NPoints(ST_Simplify(:geometry,1)) As np1_octagon,
          ST_NPoints(ST_Simplify(:geometry,10)) As np10_triangle,
          (ST_Simplify(polygon,100) is null) As  np100_geometrygoesaway
        from neighborhood_polygon where neighborhood_id = 14 ORDER BY id DESC LIMIT 1";

        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('geojson', 'geojson');

        $query = $this->em->createNativeQuery($sql,$rsm);
        $query->setParameter('regionId', $region->getId() );

        $result = $query->getSingleResult();

        die(\Zend\Debug\Debug::dump($result));
    }
} 

#select
#  ST_Npoints(polygon) As np_before,
#  ST_NPoints(ST_Simplify(polygon,0.0000000000000000000009)) As np01_notbadcircle,
#  ST_NPoints(ST_Simplify(polygon,0.80)) As np05_notquitecircle,
#  ST_NPoints(ST_Simplify(polygon,1)) As np1_octagon,
#  ST_NPoints(ST_Simplify(polygon,10)) As np10_triangle,
#  (ST_Simplify(polygon,100) is null) As  np100_geometrygoesaway
#from neighborhood_polygon where neighborhood_id = 14 ORDER BY id DESC LIMIT 1;

#SELECT ST_IsSimple(polygon) from neighborhood_polygon where neighborhood_id = 14 ORDER BY id DESC LIMIT 1;
