<?php
namespace Whathood\Model\HeatMap;

use Whathood\Spatial\PHP\Types\Geometry\LineString;
use Whathood\Spatial\PHP\Types\Geometry\Point;

/**
 * Description of BoundarySquare
 *
 * @author Jim Smiley twitter:@jimRsmiley
 */
class BoundarySquare {
    protected $yMin;
    protected $yMax;
    protected $xMin;
    protected $xMax;
    
    public function __construct( $data = null ) {
        
        if( null !== $data ) {
            $hydrator = new \Zend\Stdlib\Hydrator\ClassMethods();
            $hydrator->hydrate($data, $this);
        }
    }
    
    public function getTestPoints( $interval ) {
        
        $yDiff = abs($this->yMax - $this->yMin);
        $yIncrements = $yDiff / $interval;

        $xDiff = $this->yMax - $this->yMin;
        $xIncrements = $xDiff / $interval;

        $points = array();
        for( $y = $this->yMin; $y < $this->yMax; $y += $yIncrements ) {
            for( $x = $this->xMin; $x < $this->xMax; $x += $xIncrements ) {
                $points[] = new Point($x,$y);
            }
        }
        
        return $points;
    }
    
    public function getLineString() {
        return new LineString( array(
            new Point( $this->yMin, $this->xMin ),
            new Point( $this->yMin, $this->xMax ),
            new Point( $this->yMax, $this->xMax ),
            new Point( $this->yMax, $this->xMin )
        ));
    }
    
	public function getYMin(){
		return $this->yMin;
	}

	public function setYMin($latMin){
		$this->yMin = $latMin;
	}

	public function getYMax(){
		return $this->yMax;
	}

	public function setYMax($latMax){
		$this->yMax = $latMax;
	}

	public function getXMin(){
		return $this->xMin;
	}

	public function setXMin($lngMin){
		$this->xMin = $lngMin;
	}

	public function getXMax(){
		return $this->xMax;
	}

	public function setXMax($lngMax){
		$this->xMax = $lngMax;
	}
}
?>
