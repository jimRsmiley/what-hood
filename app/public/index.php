<?php
/**
 * This makes our life easier when dealing with paths. Everything is relative
 * to the application root now.
 */

// Report all PHP errors (see changelog)
error_reporting(E_ALL);

chdir(dirname(__DIR__));

$app_env_file = "../application_env";

if (file_exists($app_env_file)) {
    $application_env = trim(file_get_contents("../application_env"));
}
else {
    // assume it's production
    $application_env = 'production';
}

if ($application_env == 'development') {
    ini_set("display_errors",'On');
}

// Setup autoloading
require 'init_autoloader.php';

// Run the application!
Zend\Mvc\Application::init(require 'config/application.config.php')->run();
