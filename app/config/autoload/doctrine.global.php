<?php

return array(
    'doctrine' => array(
        'connection' => array(
            'orm_default' => array(
                'driverClass' => 'Doctrine\DBAL\Driver\PDOPgSql\Driver',
                'doctrine_type_mappings' => array(
                    'geometry' => 'geometry',
                    'polygon'  => 'polygon',
                    'point'    => 'point'
                ),
            ),
        ),
        'configuration' => array(
            'orm_default' => array(
                'types' => array(
                    'geometry' => 'CrEOF\Spatial\DBAL\Types\GeometryType',
                    'polygon'  => 'CrEOF\Spatial\DBAL\Types\Geometry\PolygonType',
                    'point'    => 'CrEOF\Spatial\DBAL\Types\Geometry\PointType',
                ),
                'string_functions' => array(
                    'ST_Within'     => 'Whathood\Spatial\ORM\Query\AST\Functions\MySql\STWithin',
                    'ST_Point'      => 'CrEOF\Spatial\ORM\Query\AST\Functions\PostgreSql\STPoint',
                    'ST_SetSRID'    => 'CrEOF\Spatial\ORM\Query\AST\Functions\PostgreSql\STSetSRID'
                )
            )
        ),
    ),
);
