<?php
namespace Whathood;
/**
 * Description of HeatmapJsData
 *
 * @author Jim Smiley twitter:@jimRsmiley
 */
class HeatmapJsData {
    
    protected $maxValue;
    
    public function getMaxValue() {
        return $this->maxValue;
    }
    
    public function setMaxValue( $maxValue ) {
        $this->maxValue = $maxValue;
    }
    
    public function __construct( $data = null ) {
        
        if( !empty( $data ) ) {
            $hydrator = new \Zend\Stdlib\Hydrator\ClassMethods();
            $hydrator->hydrate( $data, $this );
        }
        $this->points = array();
    }
    
    public function addPoint( $x, $y, $value ) {
        $this->points[] = array( 
            'y' => $y, 
            'x' => $x, 
            'value' => $value );
    }

    public function toJson() {
        
        $jsonData = array();
        foreach( $this->points as $r ) {
            $arr = array( 
                'lat' => $r['y'], 
                'lon' => $r['x'], 
                'value' => $r['value'] );
            array_push( $jsonData, $arr);
        }
        return \Zend\Json\Json::encode( $jsonData );
    }
}
?>
