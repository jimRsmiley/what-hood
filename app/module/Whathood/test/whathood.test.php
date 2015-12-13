<?php
return array(

    'service_manager' => array(
        'factories' => array(

            'doctrine.entitymanager.orm_default' => function($sm) {
                $doctrine = $sm->get('Whathood\Database');

                $dbName = getenv("WH_PHPUNIT_DB_NAME");
                if (empty($dbName))
                    die("must define WH_PHPUNIT_DB_NAME");

                return $doctrine->buildEntityManager($doctrine->getConfig(),
                    $doctrine->getEventManager(),
                    $dbName
                );
            },

            'doctrine.connection.orm_default' => function($sm) {
                $config = $sm->get('doctrine.configuration.orm_default');
                $dbName = getenv("WH_PHPUNIT_DB_NAME");
                if (empty($dbName))
                    die("must define WH_PHPUNIT_DB_NAME");
                $params = array(
                    'driver'   =>  'pdo_pgsql',
                    'host'     =>  (getenv('PGHOST') ? getenv('PGHOST') : 'wh-postgis'),
                    'port'     => '5432',
                    'dbname'   => $dbName,
                    'user'     => 'docker',
                    # we don't allow remote sql connections
                    'password' => null
                );
                return \Doctrine\DBAL\DriverManager::getConnection($params, $config);
            }
        )
    )
);
