<?php
/**
 * I need to be able to backup all the neighborhoods that have been added
 * to the database, and then reload them into a new schema.
 * 
 * The best way is to pull all the objects out the of the database and store
 * them in a json file.
 */
putenv('APPLICATION_ENV=development' );

$jsonFile = '../data/db_export.json';

require("Bootstrap.php");

$sm = Bootstrap::getServiceManager();

$neighborhoodPolygonMapper = $sm->get('Application\Mapper\NeighborhoodPolygonMapper');

if( false ) {
    $neighborhoods = $neighborhoodPolygonMapper->fetchAll();

    foreach( $neighborhoods as $n ) {
        print $n->getNeighborhood()->getName()."\n";
    }
    $json = \Application\Entity\NeighborhoodPolygon::neighborhoodsToJson( $neighborhoods );
    file_put_contents($jsonFile, $json );
}

if( true ) {
    $json = file_get_contents( $jsonFile );
    $neighborhoodPolygons = \Application\Entity\NeighborhoodPolygon::jsonToNeighborhoodPolygons($json);

    \Zend\Debug\Debug::dump( $neighborhoodPolygons[0] );
}
?>