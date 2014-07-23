<?php

use Whathood\Entity\Neighborhood;

/**
 * Description of NeighborhoodTest
 *
 * @author Jim Smiley twitter:@jimRsmiley
 */
class NeighborhoodTest extends PHPUnit_Framework_TestCase {
    
    public function setUp() {
        
    }
    
    public function testGetMultipleWinningUnits() {
        
        $n1 = new Neighborhood( array(
            'name'  => 'Frankford'
        ));
        $n2 = new Neighborhood( array(
            'name'  => 'Northwood'
        ));
        
        $object = new \Whathood\Model\Whathood\EntityConsensus( array( $n1, $n2 ) );
        
        $winningUnits = $object->getWinnerUnits();
        
        $this->assertEquals( 2, count( $winningUnits ) );
    }
}

?>