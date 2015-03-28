<?php
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
                    # we don't allow remote sql connections
                    'password' => null
                )
            )
        )
    ),
    'whathood' => array(
        'log' => array(
            'logfile' => '/var/log/whathood.log',
            'email' => array(
                'fromName'  => 'Whathood System',
                'fromAddress'   => 'auto-sender@whathood.in'
            )
        ),
        'concave_hull_strec' => .9,
    )
);
