<?php

return array(
    'modules' => array(
        'DoctrineModule',
        'DoctrineORMModule',
        'Application',
    ),
    'module_listener_options' => array(
        'config_glob_paths'    => array(
            '../config/autoload/'.getenv('APPLICATION_ENV').'.local.php',
        ),
        'module_paths' => array(
            'module',
            'vendor',
        ),
    ),
);
?>
