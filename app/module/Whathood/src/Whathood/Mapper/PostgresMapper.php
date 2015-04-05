<?php

namespace Whathood\Mapper;

use Doctrine\ORM\Query\ResultSetMapping;

class PostgresMapper extends BaseMapper {

    public function dbSize() {
        $sql = "SELECT pg_size_pretty( pg_database_size( current_database() ) ) As human_size
                , pg_database_size( current_database() ) As raw_size";

        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('human_size','human_size');
        $rsm->addScalarResult('raw_size','raw_size');
        $query = $this->em->createNativeQuery($sql,$rsm);
        $result = $query->getSingleResult();

        return $result['human_size'];
    }

    public function top20TableSizes() {
        $sql = "SELECT nspname || '.' || relname AS relation,
                  pg_size_pretty(pg_relation_size(C.oid)) AS size
                FROM pg_class C
                LEFT JOIN pg_namespace N ON (N.oid = C.relnamespace)
                WHERE nspname NOT IN ('pg_catalog', 'information_schema')
                  AND C.relkind <> 'i'
                  AND nspname !~ '^pg_toast'
                  AND nspname || '.' || relname != 'public.spatial_ref_sys'
                ORDER BY pg_relation_size(C.oid) DESC
                LIMIT 20";

        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('relation','relation');
        $rsm->addScalarResult('size','size');
        $query = $this->em->createNativeQuery($sql,$rsm);
        return $query->getResult();
    }
}

