<?php
/**
 * This makes our life easier when dealing with paths. Everything is relative
 * to the application root now.
 * 
 * 
 * ogr2ogr -f "KML" C:\Users\jsmiley\Development\what-hood\draft\Neighborhoods_Philadelphia\Neighborhoods_Philadelphia.kml C:\Users\jsmiley\Development\what-hood\draft\Neighborhoods_Philadelphia\Neighborhoods_Philadelphia.shp

ogr2ogr -f "KML" C:\Users\jsmiley\Development\what-hood\draft\nis_neighborhood\nis_neighborhood.kml C:\Users\jsmiley\Development\what-hood\draft\nis_neighborhood\nis_neighborhood.shp
 * 
 */

if( $argv[1] == '--development' )
    putenv('APPLICATION_ENV=development' );
else if( $argv[1] == '--production' )
    putenv('APPLICATION_ENV=production' );

require("Bootstrap.php");

$sm = Bootstrap::getServiceManager(); 


$regionMapper = $sm->get('Application\Mapper\RegionMapper');
$philadelphia = $regionMapper->byName('Philadelphia');

$kml = new Application\Spatial\NeighborhoodJsonFile\PhiladelphiaGeoJson(
                                    '../draft/phila-city_limits_shp/phila.json');
$polygon = $kml->getPolygon();
\Zend\Debug\Debug::dump( $polygon );
$philadelphia->setBorderPolygon( $polygon );
$regionMapper->save( $philadelphia );