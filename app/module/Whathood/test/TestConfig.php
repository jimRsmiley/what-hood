<?php

return array(
    'modules' => array(
        'DoctrineModule',
        'DoctrineORMModule',
        'Whathood',
    ),
    'module_listener_options' => array(
        'config_glob_paths'    => array(
            APPLICATION_PATH . '/config/autoload/development.local.php',
            APPLICATION_PATH . '/config/autoload/whathood.local.php',
        ),
        'module_paths' => array(
            'module',
            'vendor',
        ),
    ),
);
?>
