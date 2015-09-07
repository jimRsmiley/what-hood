<?php
namespace Whathood\Spatial\PHP\Types\Geometry;

use CrEOF\Spatial\PHP\Types\Geometry\Polygon as CrEOFPolygon;

class Polygon extends CrEOFPolygon {

    public static function build(array $rings, $srid) {
        if (!$srid)
            throw new \InvalidArgumentException("srid may not be empty");
        return new static($rings, $srid);
    }
}
?>
