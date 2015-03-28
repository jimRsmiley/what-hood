<?php

return array(
    'modules' => array(
        'JsMappingUtils',
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
        '../../../config/autoload/{,*.}{global,local}.php',
        ),
        'module_paths' => array(
            'module',
            'vendor'
        ),
    ),
);
?>
