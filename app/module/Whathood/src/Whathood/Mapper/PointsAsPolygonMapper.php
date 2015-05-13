<?php

namespace Whathood\Mapper;

use Doctrine\ORM\Query\ResultSetMapping;
use Whathood\Spatial\PHP\Types\Geometry\MultiPoint;
use Whathood\Spatial\PHP\Types\Geometry\Polygon;

class PointsAsPolygonMapper extends BaseMapper {

    public function toPolygon(MultiPoint $multi_point) {
        $tmp_table = "pap_tmp_tbl";
        $this->createTempTable($tmp_table);
        $this->savePoints($multi_point,$tmp_table);
        $db_val = $this->spatialPlatform()->convertToDatabaseValue($multi_point);

        $alias = 'as_text';
        $sql = "SELECT ST_AsEWKB(pgr_pointsAsPolygon('SELECT 1::int4 AS id, ST_X(point)::float8 AS x, ST_Y(point)::float8 AS y FROM $tmp_table ')) AS $alias";

        $rsm = new ResultSetMapping();
        $rsm->addScalarResult($alias,$alias);
        $query = $this->em->createNativeQuery($sql,$rsm);

        $query->setParameter(':multi_point_as_text',$db_val);
        $result = $query->getSingleResult();
        $sqlExpr = $result[$alias];

        $geom_type = \Doctrine\DBAL\Types\Type::getType('polygon');
        $polygon = $geom_type->convertToPHPValue($sqlExpr,$this->em->getConnection()->getDatabasePlatform());
        return $polygon;
    }

    public function createTempTable($tbl_name) {
        $sql = "CREATE TEMP TABLE $tbl_name (point geometry)";
        $this->em->getConnection()->query($sql);
    }

    public function savePoints(MultiPoint $multi_point,$tbl_name) {
        $db_val = $this->spatialPlatform()->convertToDatabaseValue($multi_point);

        #$sql = "INSERT INTO $tbl_name(point) VALUES ( SELECT dp.geom FROM ST_DUMP(:mp) AS dp ) )";
        $sql = "INSERT INTO $tbl_name(point) ( SELECT geom FROM ST_DUMP(ST_GeomFromText('MULTIPOINT(
            -75.079915094716 40.062337371379,-75.072915094716 40.062337371379,-75.086915094716 40.069337371379,-75.079915094716 40.069337371379,-75.072915094716 40.069337371379,-75.086915094716
         40.076337371379,-75.079915094716 40.076337371379,-75.072915094716 40.076337371379,-75.079915094716 40.083337371379,-75.072915094716 40.083337371379,-75.065915094716 40.083337371379
     )') ) )";
        $rsm = new ResultSetMapping();
        $query = $this->em->createNativeQuery($sql,$rsm)
            ->setParameter(':mp',$db_val);
        $result = $query->getResult();
    }
}
