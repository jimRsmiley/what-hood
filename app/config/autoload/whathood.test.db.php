<?php
die("in the test db setup");
return array(
    'doctrine' => array(
        'connection' => array(
            'orm_default' => array(
                'driverClass' => 'Doctrine\DBAL\Driver\PDOPgSql\Driver',
                'params' => array(
                    'host'     => 'localhost',
                    'port'     => '5432',
                    'dbname'   => 'whathood_test',
                    'user'     => 'whathood',
                    # we don't allow remote sql connections
                    'password' => null
                )
            )
        )
    ),
);
