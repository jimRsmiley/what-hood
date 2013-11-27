<?php
/**
 * I need to be able to backup all the neighborhoods that have been added
 * to the database, and then reload them into a new schema.
 * 
 * The best way is to pull all the objects out the of the database and store
 * them in a json file.
 */

if( $argv[1] == '--development' )
    putenv('APPLICATION_ENV=development' );
else if( $argv[1] == '--production' )
    putenv('APPLICATION_ENV=production' );
else
    die("you need to set --development or --production");

require("Bootstrap.php");



use Application\Entity\Neighborhood;
use Application\Entity\Region;

$options = new Zend\Console\Getopt( array(
    'sidelength|s=s'    => 'the sidelength of the boundary point search',
    'all'         => 'run a heatmap for all neighborhoods',
    'new'         => 'run a heatmap for all neighborhoods that have polygons newer than the last heatmap',
    'production'    => 'run as production',
    'development'   => 'run as development',
    'neighborhood|n=s' => 'the neighborhood name to generate a heatmap for',
    'region|r=s'    => 'the region the neighborhood is in'
));
$options->parse();

$SIDELENGTH = $options->getOption('sidelength');
// set the sidelength
$SIDELENGTH = ( $SIDELENGTH == null ? '30' : $SIDELENGTH );

$utils = new \Application\Model\ScriptUtils();
$sm = Bootstrap::getServiceManager();


$heatMapTool = new Application\Model\HeatMap\HeatMapBuilder( 
        $sm->get('Application\Mapper\HeatMapMapper' ) );

$neighborhoods = getNeighborhoods($sm,$options);


$heatMapMapper = $sm->get('Application\Mapper\HeatMapMapper');
$total = count($neighborhoods);
$count = 1;
foreach( $neighborhoods as $neighborhood ) {
    
    print $utils->tstamp() . " " . $count++ . "/" . $total . " generating heatmap for " . $neighborhood->getName() . ', ' 
                                . $neighborhood->getRegion()->getName() . "\n";
    
    $heatMap = $heatMapTool->getLatestHeatMap(
            $neighborhood,
            $neighborhood->getRegion(), $SIDELENGTH );
    
    if( !empty( $heatMap ) )
        $heatMapMapper->save($heatMap);
}

print $utils->tstamp() . " done\n";

function getNeighborhoods($sm,$options) {
    // make sure we have the neighborhood information we need
    $neighborhoodName = $options->getOption('neighborhood');
    $regionName = $options->getOption('region');
    $runAll = $options->getOption('all');
    $runNew = $options->getOption('new');
    
    // don't let a runAll flag and single neighborhoods go at the same time
    if( !empty($runAll) && ( !empty($neighborhoodName)||!empty($regionName) ) )
        die("you can't run a neighborhood and all neighborhoods at the same time");

// don't let a runAll flag and single neighborhoods go at the same time
    if( !empty($runNew) && ( !empty($neighborhoodName)||!empty($regionName) ) )
        die("you can't run a neighborhood and all new neighborhoods at the same time");
    
    if( !empty($runAll) ) {
        $neighborhoodMapper = $sm->get('Application\Mapper\NeighborhoodMapper');
        $neighborhoods = $neighborhoodMapper->fetchAll();
    }
    else if( !empty( $runNew) ) {
        //$neighborhoodMapper = $sm->get('Application\Mapper\NeighborhoodMapper');
        //$neighborhoods = $neighborhoodMapper->fetchAll();
        $heatMapMapper = $sm->get('Application\Mapper\HeatMapMapper');
        $neighborhoods = $heatMapMapper->getStaleNeighborhoods();
    }
    else {
        $neighborhoods = array( 
            new Neighborhood( array(
                    'name' => $neighborhoodName,
                    'region' => new Region( array(
                            'name' => $regionName
                    ))
            ))
        );
    }
    return $neighborhoods;
}
?>