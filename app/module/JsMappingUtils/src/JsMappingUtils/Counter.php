<?php
namespace JsMappingUtils;
/**
 * Description of Counter
 *
 * @author Jim Smiley twitter:@jimRsmiley
 */
class Counter {
    
    protected $count;
    
    public function __construct() {
        $this->count=0;
    }
    
    public function increment() {
        $this->count++;
    }
    
    public function modulo($int) {
        return ( $this->count % $int ) == 0;
    }
    
    public function getCount() {
        return $this->count;
    }
}

?>
