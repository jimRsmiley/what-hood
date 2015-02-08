<?php

namespace Whathood;

use CrEOF\Spatial\PHP\Types\Geometry\Polygon as CrEOFPolygon;
use CrEOF\Spatial\PHP\Types\Geometry\Point;
use Whathood\Spatial\PHP\Types\Geometry\LineString;

class Polygon {

    /**
     * expects an array that conforms to a geojson object
     **/
    public static function buildPolygonFromGeoJsonArray(array $polygon_array,$srid) {
        if (empty($polygon_array))
            throw new \InvalidArgumentException("polygon_array may not be empty");

        $lineStringArray = $polygon_array['geometry']['coordinates'];
        $ring = array();

        foreach( $lineStringArray as $lineString ) {
            foreach( $lineString as $point ) {
                $ring[] = new Point( $point[1], $point[0] );
            }
        }
        $myLineString = new LineString( $ring );
        $myLineString->close();
        $polygon = new CrEOFPolygon( $rings = array($myLineString));
        $polygon->setSRID($srid);
        return $polygon;
    }
}
