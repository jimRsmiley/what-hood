<?php
/**
 * I need to be able to backup all the neighborhoods that have been added
 * to the database, and then reload them into a new schema.
 * 
 * The best way is to pull all the objects out the of the database and store
 * them in a json file.
 */

require("Bootstrap.php");
$sm = Bootstrap::getServiceManager();

$jsonFile = '../data/db_export.json';

$json = file_get_contents($jsonFile);

$neighborhoods = \Application\Entity\Neighborhood::jsonToNeighborhoods( $json );

$em = Bootstrap::getServiceManager()->get('mydoctrineentitymanager');
$schemaTool = Bootstrap::getServiceManager()->get('Application\SchemaTool');

$schemaTool->dropSchema();
$schemaTool->createSchema();
$schemaTool->setAutoIncrementNeighborhoodIds(true);
$schemaTool->addNeighborhoods($neighborhoods);
?>