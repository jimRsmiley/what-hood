<?php

return array(
    'modules' => array(
        'Whathood',
        'DoctrineModule',
        'DoctrineORMModule',
        'ZfcBase',
        'ZfcUser',
        'ZfcUserDoctrineORM',
        'BjyAuthorize',
        'SamUser'
    ),
    'module_listener_options' => array(
        'config_glob_paths'    => array(
        '../../../config/autoload/whathood.test.db.php',
        '../../../config/autoload/whathood.global.php',
        ),
        'module_paths' => array(
            'module',
            'vendor'
        ),
    ),
);
?>
