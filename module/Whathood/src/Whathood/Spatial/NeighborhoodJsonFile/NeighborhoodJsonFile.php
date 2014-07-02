<?php
namespace Whathood\Spatial\NeighborhoodJsonFile;

use Whathood\Entity\Neighborhood;
use Whathood\Entity\WhathoodUser;
use Whathood\Entity\UserPolygon;
use Whathood\Entity\Region;
use Whathood\Spatial\PHP\Types\Geometry\Point;
use Whathood\Spatial\PHP\Types\Geometry\Polygon;
use Whathood\Spatial\PHP\Types\Geometry\LineString;
/**
 * Description of NeighborhoodJson
 *
 * @author Jim Smiley twitter:@jimRsmiley
 */
abstract class NeighborhoodJsonFile {
    
    protected $file = null;
    
    protected $whathoodUser;
    
    public function setFile( $file ) {
        $this->file = $file;
    }
    
    public abstract function getRegion();
    
    public abstract function getWhathoodUser();
    
    public function setWhathoodUser( $whathoodUser ) {
        $this->whathoodUser = $whathoodUser;
    }
    
    public abstract function isAuthority();
    
    public function getPolygon() {
        
        $filename = $this->file;
        
        if( !file_exists($filename))
            throw new \InvalidArgumentException("$filename does not exist");
        
        if( $this->getRegion() == null )
            throw new \InvalidArgumentException("region must be defined");
        
        if( $this->getWhathoodUser() == null )
            throw new \InvalidArguementException("user must be defined");
        
        $jsonStr = file_get_contents( $filename );
        
        $whathoodUser = $this->getWhathoodUser();
        
        $json = \Zend\Json\Json::decode($jsonStr);
        
        $neighborhoods = array();
        
        $counter = 1;
        foreach( $json->kml->Document->Folder->Placemark as $n ) {
            
            $name = $n->ExtendedData->SchemaData->SimpleData[2]->{'#text'};
            $coordinateString = $n->Polygon->outerBoundaryIs->LinearRing->coordinates;
            
            $array = explode( ' ', $coordinateString );
            
            $neighborhood = new Neighborhood();
            $neighborhood->setName($name);
            $neighborhood->setRegion( $this->getRegion() );
            
            
            $points = array();
            foreach( $array as $point ) {
                list($lng,$lat) = explode(",", $point );
                $points[] = new Point(  $lng, $lat );
            }
            $lineString = new LineString( $points );
            $lineString->close();
            $polygon = new Polygon( array( $lineString) );

            $neighborhoodPolygon = new UserPolygon();
            $neighborhoodPolygon->setWhathoodUser( $whathoodUser );
            $neighborhoodPolygon->setAuthority( $this->isAuthority() );
            $neighborhoodPolygon->setNeighborhood($neighborhood);
            $neighborhoodPolygon->setPolygon( $polygon );
            $neighborhoodPolygon->setRegion( $this->getRegion() );
            
            $neighborhoods[] = $neighborhoodPolygon;
        }
        
        return $neighborhoods;
    }
}

?>
 