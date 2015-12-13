<?php
namespace Whathood\Spatial\PHP\Types\Geometry;

use CrEOF\Spatial\PHP\Types\Geometry\Polygon as CrEOFPolygon;

class Polygon extends CrEOFPolygon {

    /**
     * build a new Polygon given rings and srid
     */
    public static function build(array $rings, $srid) {
        if (!$srid)
            throw new \InvalidArgumentException("srid may not be empty");
        return new static($rings, $srid);
    }

    public static function buildFromParent(CrEOFPolygon $parent) {
        $obj = new static($parent->getRings(), $parent->getSRID());
        return $obj;
    } 
}
