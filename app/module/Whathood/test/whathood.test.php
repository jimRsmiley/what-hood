<?php
return array(

    'service_manager' => array(
        'factories' => array(
            'my_factory_test' => function($sm) {
                die("yup here");
            },
            'doctrine.entitymanager.orm_default' => function($sm) {
                $doctrine = $sm->get('Whathood\Doctrine');

                $dbName = getenv("WHATHOOD_DB");
                if (empty($dbName))
                    die("must define WHATHOOD_DB");

                return $doctrine->buildEntityManager($doctrine->getConfig(),
                    $doctrine->getEventManager(),
                    $dbName
                );
            }
        )
    )
);
