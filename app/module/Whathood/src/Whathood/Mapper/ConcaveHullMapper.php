<?php

namespace Whathood\Mapper;

use Doctrine\ORM\Query\ResultSetMapping;
use Whathood\Spatial\PHP\Types\Geometry\MultiPoint;
use Whathood\Spatial\PHP\Types\Geometry\Polygon;

class ConcaveHullMapper extends BaseMapper {

    public function toPolygon(MultiPoint $multi_point,$grid_resolution) {

        $db_val = $this->spatialPlatform()->convertToDatabaseValue($multi_point);

        $alias = 'as_text';
        $sql = "SELECT ST_AsEWKB(ST_ConcaveHull( ST_GeomFromText(:multi_point_as_text),:grid_resolution)) as $alias";

        $rsm = new ResultSetMapping();
        $rsm->addScalarResult($alias,$alias);
        $query = $this->em->createNativeQuery($sql,$rsm);
        $query->setParameter(':multi_point_as_text',$db_val);
        $query->setParameter(':grid_resolution',$grid_resolution);
        $result = $query->getSingleResult();
        $sqlExpr = $result[$alias];

        $geom_type = \Doctrine\DBAL\Types\Type::getType('polygon');
        $polygon = $geom_type->convertToPHPValue($sqlExpr,$this->em->getConnection()->getDatabasePlatform());
        return $polygon;
    }
}
