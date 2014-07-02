<?php
namespace Whathood\View\Model;
/**
 * We're already getting geojson from the database, I need be able to send that
 * text back but with proper headers as json content. Zend's JsonModel will
 * encode the variables
 *
 * @author Jim Smiley twitter:@jimRsmiley
 */
class JsonModel extends \Zend\View\Model\JsonModel {
    
    protected $jsonStr;
    
    public function __construct( $jsonStr ) {
        $this->jsonStr = $jsonStr;
    }
    /**
     * Serialize to JSON
     *
     * @return string
     */
    public function serialize()
    {
        return $this->jsonStr;
    }
}

?>
