<?php
namespace JsMappingUtils\Spatial;

use CrEOF\Spatial\PHP\Types\Geometry\Point;
use CrEOF\Spatial\PHP\Types\Geometry\Polygon;
use CrEOF\Spatial\PHP\Types\Geometry\LineString;
/**
 * Philadelphia needs be broken up into grid squares
 *
 * @author Jim Smiley twitter:@jimRsmiley
 */
class PolygonUtils {
    
    public static function getGridSquares($xmin,$xmax,$ymin,$ymax,$resolution,$srid) {
        
        $polygons = array();
        for( $x = $xmin; $x < $xmax; $x += $resolution ) {
            for( $y = $ymin; $y < $ymax; $y += $resolution ) {
                $ring = array(
                    new Point( $x, $y ),
                    new Point( $x+$resolution, $y ),
                    new Point( $x+$resolution, $y+$resolution ),
                    new Point( $x, $y+$resolution ),
                );
                $ring = PolygonUtils::closeRing($ring);
                $polygons[] = new Polygon( array( $ring ),$srid );
            }
        }
        
        return $polygons;
    }
    
    public static function textToPolygon( $text ) {
        
        try {
            $parser = new \CrEOF\Spatial\DBAL\Types\StringParser($text);
            $data = $parser->parse();
            $polygon = new \CrEOF\Spatial\PHP\Types\Geometry\Polygon( 
                                    $data['value'], $data['srid'] );
            return $polygon;
        } catch( \CrEOF\Spatial\Exception\InvalidValueException $e ) {
            return null;
        }
    }
    
    public static function getSpacedPoints($xmin,$xmax,$ymin,$ymax,$resolution,$srid) {
        
        $points = array();
        for( $x = $xmin; $x < $xmax; $x += $resolution ) {
            for( $y = $ymin; $y < $ymax; $y += $resolution ) {
                $ring = array(
                    new Point( $x, $y ),
                    new Point( $x+$resolution, $y ),
                    new Point( $x+$resolution, $y+$resolution ),
                    new Point( $x, $y+$resolution ),
                );
                $ring = PolygonUtils::closeRing($ring);
            }
        }
        
        return $points;
    }
    
    public static function getCenter( $lineString, $srid ) {
        
        $x1 = $lineString->getPoint(0)->getX();
        $x2 = $lineString->getPoint(2)->getX();
        $centerX = ( $x1 + $x2 ) / 2;
        
        $y1 = $lineString->getPoint(0)->getY();
        $y2 = $lineString->getPoint(2)->getY();
        $centerY = ( $y1 + $y2 ) / 2;
        
        return new Point( $centerX, $centerY, $srid );
    }
    
    public static function closeRing( $ring ) {
        $ring[] = $ring[0];
        return $ring;
    }
    
    public static function geoJsonToPolygon( $featureArray ) {
        
        if( is_string($featureArray) ) {
            $array = \Zend\Json\Json::decode( $featureArray );
        }
        else
            $array = $featureArray;
        
        if( $array->type !== 'Polygon' )
            throw new InvalidArgumentException( "expecting feature array to be type Polygon");
        
        $lineStrings = array();
        foreach( $array->coordinates as $coordinateRing ) {
            $points = array();
            foreach( $coordinateRing as $poing ) {
                $points[] = new Point( $poing[0], $poing[1] );
            }
            $lineStrings[] = new LineString( $points );
        }
        return new Polygon( $lineStrings );
    }
}
?>
