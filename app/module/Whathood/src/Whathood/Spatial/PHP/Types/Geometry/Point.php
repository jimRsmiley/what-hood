<?php
namespace Whathood\Spatial\PHP\Types\Geometry;

use CrEOF\Spatial\PHP\Types\Geometry\Point as CrEOFPoint;
/**
 * Description of Polygon
 *
 * @author Jim Smiley twitter:@jimRsmiley
 */
class Point extends CrEOFPoint {

    /**
     *  build a Point object from a string like 'Point(x y)'
     *
     *  @param string $str
     *
     *  @return Point
     */
    public static function buildFromText($str) {
        $matches = array();
        if (!preg_match('/POINT\((.+)\s(.+)\)/',$str,$matches)) {
            throw new \InvalidArgumentException("string '$str' does not appear to be a valid Point representation");
        }
        return new Point($matches[1],$matches[2]);
    }
}

?>
