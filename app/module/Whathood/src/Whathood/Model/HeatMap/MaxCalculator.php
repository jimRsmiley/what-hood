<?php

namespace Whathood\Model\HeatMap;
/**
 * I need a class that will will determine the heat map's top of scale.  I don't
 * want to set a max value if it only occurs once, let's use the mostly occurring
 * max value.
 *
 * @author Jim Smiley twitter:@jimRsmiley
 */
class MaxCalculator {
    
    // should be $distributions[maxNumAsString] = count
    protected $distribution;
    
    public function __construct() {
        $this->distribution = array();
    }
    
    public function add( $maxValue ) {
    
        if( array_key_exists( $maxValue, $this->distribution ) )
            $this->distribution[(string)$maxValue]++;
        else
            $this->distribution[(string)$maxValue] = 1;
    }
    
    public function getMaxValue() {
        asort( $this->distribution );
        end( $this->distribution );
        return key( $this->distribution );
    }
}

?>
