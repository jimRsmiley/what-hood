<?php
print "running test_postgres.php\n";
putenv('APPLICATION_ENV=development' );

require("Bootstrap.php");



$sm = Bootstrap::getServiceManager();

$neighborhoodPolygonMapper = $sm->get('Application\Mapper\NeighborhoodPolygonMapper');
$np = $neighborhoodPolygonMapper->byId( '23' );

\Zend\Debug\Debug::dump( $np->getPolygon() );