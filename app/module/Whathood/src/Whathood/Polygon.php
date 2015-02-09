<?php

namespace Whathood;

use CrEOF\Spatial\PHP\Types\Geometry\Polygon as CrEOFPolygon;
use CrEOF\Spatial\PHP\Types\Geometry\Point;
use Whathood\Spatial\PHP\Types\Geometry\LineString;

class Polygon {

    /**
     * expects an array that conforms to a geojson object
     **/
    public static function buildPolygonFromGeoJsonString($json_str,$srid) {
        if (empty($json_str))
            throw new \InvalidArgumentException("json_str may not be empty");

        $polygon_array = \Zend\Json\Json::decode($json_str,\Zend\Json\Json::TYPE_ARRAY);

        return static::buildPolygonFromGeoJsonArray($polygon_array,$srid);
    }

    /**
     * expects an array that conforms to a geojson object
     **/
    public static function buildPolygonFromGeoJsonArray(array $polygon_array,$srid) {
        if (empty($polygon_array))
            throw new \InvalidArgumentException("polygon_array may not be empty");

        if (isset($polygon_array['geometry']['coordinates'])) {
            $lineStringArray = $polygon_array['geometry']['coordinates'];
        }
        else if(isset($polygon_array['coordinates'])) {
            $lineStringArray = $polygon_array['coordinates'];
        }
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
