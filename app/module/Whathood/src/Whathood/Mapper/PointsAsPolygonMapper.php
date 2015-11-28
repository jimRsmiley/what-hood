<?php

namespace Whathood\Mapper;

use Doctrine\ORM\Query\ResultSetMapping;
use Whathood\Spatial\PHP\Types\Geometry\MultiPoint;
use Whathood\Spatial\PHP\Types\Geometry\Polygon;

/**
 *
 *  - create a temp table
 * first store the multi_point in a temp table so that it can be called by pgr_pointsAsPolygon
 *
 */
class PointsAsPolygonMapper extends BaseMapper {

    // the minimum number of points that it will process
    protected $_min_multi_points = 7;

    protected $_tmp_table_name = "pap_tmp_tbl";
    protected $_current_multi_point = null;

    /**
     * create a temp table
     * save the multipoints into it
     * query the temp table with pgr_pointsAsPolygon to create the neighborhood border
     **/
    public function toPolygon(MultiPoint $multi_point) {
        try {
            if (empty($multi_point->getPoints()))
                throw new \InvalidArgumentException("multi_point must contain points");
            if (count($multi_point->getPoints()) < $this->_min_multi_points)
                throw new \Whathood\Exception("multi_point must contain at least ".$this->_min_multi_points . " points; got ".count($multi_point->getPoints()));
            $this->_current_multi_point = $multi_point;

            # db interaction 
            $this->dropTempTable($this->_tmp_table_name);

            # db interaction 
            $this->createTempTable($this->_tmp_table_name);

            # db interaction 
            $this->savePoints($multi_point,$this->_tmp_table_name);

            # db interaction 
            $polygon = $this->pointsAsPolygon($this->_tmp_table_name,count($multi_point->getPoints()));

            $this->dropTempTable($this->_tmp_table_name);
        }
        catch(\Exception $e) {
            #$this->em->getConnection()->rollback();
            throw $e;
        }
        return $polygon;
    }

    protected function pointsAsPolygon($tmp_table_name,$num_points) {
        $alias = 'as_text';
        $sql = "SELECT ST_AsEWKB(pgr_pointsAsPolygon('SELECT 1::int4 AS id, ST_X(point)::float8 AS x, ST_Y(point)::float8 AS y FROM $tmp_table_name '::text)) AS $alias";

        $rsm = new ResultSetMapping();
        $rsm->addScalarResult($alias,$alias);
        $query = $this->em->createNativeQuery($sql,$rsm);

        try {
            $result = $query->getSingleResult();
            $sqlExpr = $result[$alias];
        }
        catch(\Doctrine\DBAL\Exception\DriverException $e) {
            if (strpos($e->getMessage(),"Distance is too short. only 2 vertices for alpha shape calculation")) {
                $errMsg = "failed to build polygon using $num_points points; distance is too short";
                throw new \Whathood\Exception($errMsg, $code=0, $e);
            }
            else {
                throw $e;
            }
        }
        $geom_type = \Doctrine\DBAL\Types\Type::getType('polygon');
        $polygon = $geom_type->convertToPHPValue($sqlExpr,$this->em->getConnection()->getDatabasePlatform());
        return $polygon;
    }

    public function dropTempTable($tbl_name) {
        $sql = "DROP TABLE IF EXISTS $tbl_name";
        $this->em->getConnection()->query($sql);
    }
    public function createTempTable($tbl_name) {
        $sql = "CREATE TEMP TABLE $tbl_name (point geometry)";
        $this->em->getConnection()->query($sql);
    }

    public function savePoints(MultiPoint $multi_point,$tbl_name) {

        $db_val = $this->spatialPlatform()->convertToDatabaseValue($multi_point);

        $sql = "INSERT INTO $tbl_name(point) (
            SELECT geom FROM ST_DUMP(ST_GeomFromText(:mp) )
        )";
        $rsm = new ResultSetMapping();
        $query = $this->em->createNativeQuery($sql,$rsm)
            ->setParameter(':mp',$db_val);
        $result = $query->getResult();
    }
}
