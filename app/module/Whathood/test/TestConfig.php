<?php

$app_root = "../../..";

return array(
    'modules' => array(
        'Whathood',
        'DoctrineModule',
        'DoctrineORMModule',
        'ZfcBase',
        'ZfcUser',
        'ZfcUserDoctrineORM',
        'BjyAuthorize',
        'SamUser',
        'SlmQueue',
        'SlmQueueDoctrine'
    ),
    'module_listener_options' => array(
        'config_glob_paths'    => array(
            $app_root . '/config/autoload/whathood.db.php',
            $app_root . '../../../config/autoload/*.global.php',
        ),
        'module_paths' => array(
            $app_root . '/module',
            $app_root . '/vendor'
        ),
    ),
);
?>
