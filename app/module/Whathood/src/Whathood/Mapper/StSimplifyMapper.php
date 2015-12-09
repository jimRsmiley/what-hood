<?php

namespace Whathood\Mapper;

class StSimplifyMapper extends BaseMapper {

    public function simplify(Polygon $polygon, $tolerance) {
        $dql = "SELECT ST_Simplify(:geometry) as simplified";

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
