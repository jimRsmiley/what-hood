<?php

namespace Whathood\Spatial;

use CrEOF\Spatial\PHP\Types\Geometry\Polygon;
use Whathood\Spatial\PHP\Types\Geometry\MultiPoint;

class Util {

    public static function toGeoJsonArray($geom) {
        $class = get_class($geom);
        if ($geom instanceof Polygon)
            return static::polygonToGeoJsonArray($geom);
        if ($geom instanceof MultiPoint)
            return static::multiPointToGeoJsonArray($geom);
        throw new \InvalidArgumentException("do not know how to convert type $class");
    }

    public static function multiPointToGeoJsonArray($multi_point) {
        $coordinates = array();
        foreach( $multi_point->getPoints() as $p ) {
            array_push( $coordinates, $p->toArray() );
        }
        return array(
            'type' => 'MultiPoint',
            'coordinates' => $coordinates
        );
    }

    public static function polygonToGeoJsonArray($polygon) {
        $coordinates = array();

        foreach( $polygon->getRings() as $ring ) {
            array_push( $coordinates, $ring->toArray() );
        }

        $arr = array(
            'type'      => 'Polygon',
            'coordinates' => $coordinates
        );

        return $arr;
    }
}
