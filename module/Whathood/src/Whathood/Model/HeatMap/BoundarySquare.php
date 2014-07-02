<?php
namespace Application\Model\HeatMap;

use Application\Spatial\PHP\Types\Geometry\LineString;
use Application\Spatial\PHP\Types\Geometry\Point;
/**
 * Description of BoundarySquare
 *
 * @author Jim Smiley twitter:@jimRsmiley
 */
class BoundarySquare {
    protected $latMin;
    protected $latMax;
    protected $lngMin;
    protected $lngMax;
    
    public function __construct( $data ) {
        $hydrator = new \Zend\Stdlib\Hydrator\ClassMethods();
        $hydrator->hydrate($data, $this);
    }
    
    public function getTestPoints( $interval ) {
        $latDiff = abs($this->latMax - $this->latMin);
        $latIncrements = $latDiff / $interval;

        $lngDiff = $this->latMax - $this->latMin;
        $lngIncrements = $lngDiff / $interval;

        $points = array();
        for( $lat = $this->latMin; $lat < $this->latMax; $lat += $latIncrements ) {
            for( $lng = $this->lngMin; $lng < $this->lngMax; $lng += $lngIncrements ) {
                $points[] = new \Application\Spatial\PHP\Types\Geometry\Point($lat,$lng);
            }
        }
        
        return $points;
    }
    
    public function getLineString() {
        return new LineString( array(
            new Point( $this->latMin, $this->lngMin ),
            new Point( $this->latMin, $this->lngMax ),
            new Point( $this->latMax, $this->lngMax ),
            new Point( $this->latMax, $this->lngMin )
        ));
    }
    
	public function getLatMin(){
		return $this->latMin;
	}

	public function setLatMin($latMin){
		$this->latMin = $latMin;
	}

	public function getLatMax(){
		return $this->latMax;
	}

	public function setLatMax($latMax){
		$this->latMax = $latMax;
	}

	public function getLngMin(){
		return $this->lngMin;
	}

	public function setLngMin($lngMin){
		$this->lngMin = $lngMin;
	}

	public function getLngMax(){
		return $this->lngMax;
	}

	public function setLngMax($lngMax){
		$this->lngMax = $lngMax;
	}


}

?>
