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
    
$schemaTool = new \Application\SchemaTool($sm);


$region = new \Application\Entity\Region( array( 
                'name' => 'Philadelphia',
                'centerPoint' => new CrEOF\Spatial\PHP\Types\Geometry\Point(39.97735,-75.139961)
        ));

$azeveaFile = $sm->get('Application\Spatial\NeighborhoodJsonFile\Azavea' );
$azeveaFile->setFile( '../draft/Philadelphia_neighborhoods.json' );

$schemaTool->dropSchema();
$schemaTool->createSchema();

$whathoodUserMapper = $sm->get('Application\Mapper\WhathoodUserMapper');

$auser = $azeveaFile->getWhathoodUser();
$whathoodUserMapper->save( $auser );
$azeveaFile->setWhathoodUser( $auser );

loadPolygons($sm,$azeveaFile);


function loadPolygons( $sm, $polygonFile ) {
    
    $neighborhoodPolygons = $polygonFile->getPolygon();

    $neighborhoodPolygonMapper = 
                    $sm->get('Application\Mapper\NeighborhoodPolygonMapper');
    foreach( $neighborhoodPolygons as $n ) {

        $neighborhoodPolygonMapper->save($n);
        print getenv("APPLICATION_ENV") . " saved " 
            . $n->getWhathoodUser()->getUserName() 
                . "'s " 
                .  $n->getNeighborhood()->getName() 
                . " id="
                . $n->getId()
                . "\n";
    }    
}
