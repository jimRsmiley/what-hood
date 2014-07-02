<?php
namespace Application\Model\Whathood;
/**
 * Description of WhatHoodResult
 *
 * @author Jim Smiley twitter:@jimRsmiley
 */
class WhathoodResult {
    
    protected $address = null;
    protected $lat = null;
    protected $lng = null;
    protected $regionName = null;
    protected $neighborhoods = null;
    protected $consensus = null;
    
    public function __construct() {
        $this->consensus = new WhathoodConsensus();
    }

    public function setLatLng($lat, $lng ) {
        $this->lat = $lat;
        $this->lng = $lng;
    }
    
    public function getLat() {
        return $this->lat;
    }
    
    public function getLng() {
        return $this->lng;
    }
    
    public function getAddress() {
        return $this->address;
    }
    
    public function setAddress( $address ) {
        $this->address = $address;
    }
    
    public function setRegionName( $regionName ) {
        $this->regionName = $regionName;
    }
    
    public function getRegionName() {

        if( count( $this->neighborhoods ) > 0 && $this->regionName == null )
            return $this->neighborhoods[0]->getRegion()->getName();
        
        return $this->regionName;
    }
    
    public function setNeighborhoods( $neighborhoods ) {
        $this->neighborhoods = $neighborhoods;

        $this->consensus->addNeighborhoods( $neighborhoods );
    }
    
    public function getConsensus() {
        return $this->consensus;
    }
    
    public function toJson() {
        return \Zend\Json\Json::encode( $this->toArray() );
    }
    
    public function toArray() {

        return array(
                'request' => array(
                    'address' => $this->getAddress(),
                    'lat'   => $this->getLat(),
                    'lng'   => $this->getLng()
                ),
                'response'  => array(
                    'consensus'   => $this->consensus->toArray(),
                ),
        );
    }
}

?>
