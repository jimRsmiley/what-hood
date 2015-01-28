<?php
/* whathood's zend config file */
return array(
    'doctrine' => array(
        'connection' => array(
            'orm_default' => array(
                'driverClass' => 'Doctrine\DBAL\Driver\PDOPgSql\Driver',
                'params' => array(
                    'host'     => 'localhost',
                    'port'     => '5432',
                    'dbname'   => 'whathood',
                    'user'     => 'whathood',
                    'password' => null
                )
            )
        )
    ),
);
