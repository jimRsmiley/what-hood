<?php

date_default_timezone_set('America/New_York');

// Report all PHP errors (see changelog)
error_reporting(E_ALL);

/**
 * This makes our life easier when dealing with paths. Everything is relative
 * to the application root now.
 */
$APP_ROOT = dirname(__DIR__);
chdir($APP_ROOT);

putenv("APPLICATION_ROOT=$APP_ROOT");

$application_env = getenv('APPLICATION_ENV');

if ($application_env == 'development') {
    ini_set("display_errors",'On');
}

// Setup autoloading
require 'init_autoloader.php';

// Run the application!
Zend\Mvc\Application::init(require 'config/application.config.php')->run();
