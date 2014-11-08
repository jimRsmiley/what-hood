<?php
namespace JsMappingUtils;
/**
 * Description of Timer
 *
 * @author Jim Smiley twitter:@jimRsmiley
 */
class Timer {
    public static function report( $msg = null) {
       print date("H:i:s"). " " . $msg . "\n";
    }
}

?>
