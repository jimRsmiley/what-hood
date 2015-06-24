<?php
return array(
    'doctrine' => array(
        'connection' => array(
            'orm_default' => array(
                'driverClass' => 'Doctrine\DBAL\Driver\PDOPgSql\Driver',
                'params' => array(
                    'host'     =>  (getenv('PGHOST') ? getenv('PGHOST') : 'wh-postgis'),
                    'port'     => '5432',
                    'dbname'   => 'whathood',
                    'user'     => 'docker',
                    # we don't allow remote sql connections
                    'password' => null
                )
            )
        )
    ),
);
