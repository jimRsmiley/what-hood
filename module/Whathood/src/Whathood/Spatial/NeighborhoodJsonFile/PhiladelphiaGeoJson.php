<?php
namespace Application\Spatial\NeighborhoodJsonFile;

use Application\Entity\WhathoodUser;
use Application\Entity\Neighborhood;
use Application\Entity\NeighborhoodPolygon;
use Application\Entity\Region;
use CrEOF\Spatial\PHP\Types\Geometry\Point;
use Application\Spatial\PHP\Types\Geometry\Polygon;
use Application\Spatial\PHP\Types\Geometry\LineString;

class PhiladelphiaGeoJson {
    
    protected $filename;
    
    public function __construct( $filename ) {
        $this->filename = $filename;
    }
    
    public function getPolygon() {
        $filename = $this->filename;
        
        if( !file_exists($filename))
            throw new \InvalidArgumentException("$filename does not exist");
        
        $jsonStr = file_get_contents( $filename );
        
        $json = \Zend\Json\Json::decode($jsonStr);
        
        $counter = 1; $points = array();
        foreach( $json->features[0]->geometry->coordinates[0] as $point ) {
            $lat = $point[1];
            $lng = $point[0];
            $points[] = new Point( $lat, $lng );
        }
        $lineString = new LineString( $points );
        $lineString->close();
        
        return new Polygon( array( $lineString) );
    }
}

?>
