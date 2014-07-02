<?php

return array(
    'modules' => array(
        'DoctrineModule',
        'DoctrineORMModule',
        'Whathood',
    ),
    'module_listener_options' => array(
        'config_glob_paths'    => array(
            '../../../../config/autoload/test.local.php',
            '../../../../config/autoload/whathood.local.php',
        ),
        'module_paths' => array(
            'module',
            'vendor',
        ),
    ),
);
?>
