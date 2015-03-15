<?php
namespace Whathood\Spatial\DBAL\Types\Geometry;

use Whathood\Spatial\PHP\Types\Geometry\Polygon as WhathoodPolygon;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use CrEOF\Spatial\DBAL\Types\BinaryParser;
/**
 * Need to override CrEOF type to insert a Whathood polygon
 *
 * @author Jim Smiley twitter:@jimRsmiley
 */
class PolygonType extends \CrEOF\Spatial\DBAL\Types\Geometry\PolygonType {

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        if (null === $value) {
            return null;
        }

        if (ctype_alpha($value[0])) {
            return $this->getSpatialPlatform($platform)->convertStringToPHPValue($value);
        }

        return $this->getSpatialPlatform($platform)->convertBinaryToPHPValue($value);
    }

    /**
     * Create spatial object from parsed value
     *
     * @param array $value
     *
     * @return GeometryInterface
     * @throws \CrEOF\Spatial\Exception\InvalidValueException
     */
    private function newObjectFromValue($value)
    {
        return new WhathoodPolygon($value['value'], $value['srid']);
    }
}

?>
