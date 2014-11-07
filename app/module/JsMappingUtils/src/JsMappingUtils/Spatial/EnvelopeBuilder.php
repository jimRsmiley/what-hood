<?php

namespace JsMappingUtils\Spatial;

/**
 * Given an array of points, this class will get a bounding box that encompasses them
 *
 * @author Jim Smiley twitter:@jimRsmiley
 */
class EnvelopeBuilder {
    
    public static function geoJsonToEnvelope( $json ) {
        $array = $json;
        
        if( is_string( $array ) ) {
            $array = \Zend\Json\Json::decode( $array );
        }
        $points = $array->coordinates[0];
        
        return new Envelope( array(
                'yMin'  => self::ymin($points),
                'yMax'  => self::ymax($points),
                'xMin'  => self::xmin($points),
                'xMax'  => self::xmax($points)
            )
        );
    }
    
    /**
     * given an array of points, return the min y(lng)
     * 
     * @param type $points an array of points
     */
    public static function ymin($points) {
    
        $ymin = null;
        foreach( $points as $point ) {
            $pointY = $point[1];
            if( $ymin == null || $pointY < $ymin )
                $ymin = $pointY;
        }

        return floatval($ymin);
    }

    /**
    * given an array of points, return the max y(lng)
    * 
    * @param type $points an array of points
    */
    public static function ymax($points) {

        $ymax = null;
        foreach( $points as $point ) {
            $pointY = $point[1];
            if( $ymax == null || $pointY > $ymax )
                $ymax = $pointY;
        }

        return floatval($ymax);
    }

    /**
    * given an array of points, return the min x(lat)
    * 
    * @param type $points an array of points
    */
    public static function xmin($points) {

        $xmin = null;
        foreach( $points as $point ) {
            $pointX = $point[0];
            if( $xmin == null || $pointX < $xmin )
                $xmin = $pointX;
        }

        return floatval($xmin);
    }

    /**
    * given an array of points, return the max x(lat)
    * 
    * @param type $points an array of points
    */
    public static function xmax($points) {

        $xmax = null;
        foreach( $points as $point ) {
            $pointX = $point[0];
            if( $xmax == null || $pointX > $xmax )
                $xmax = $pointX;
        }

        return floatval($xmax);
    }
}

?>
