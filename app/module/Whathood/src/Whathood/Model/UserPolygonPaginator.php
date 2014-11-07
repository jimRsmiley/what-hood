<?php
namespace Whathood\Model;
/**
 * Description of NeighborhoodPaginator
 *
 * @author Jim Smiley twitter:@jimRsmiley
 */
class UserPolygonPaginator extends \Zend\Paginator\Paginator {
    
    protected $uriParams;
    
    public function setUriParams( $array ) {
        $this->uriParams = $array;
    }

    public function pageUrl($pageNum) {
        $this->uriParams['page'] = $pageNum;
        return $this->getPageUrl();
    }
    
    public function getPageUrl() {
        $url = "/n";
        
        $params = $this->uriParams;
        if( isset( $params['page'] ) )
            $url .= '/page/'.$this->uriParams['page'];
        else
            $url .= '/page/1';
        
        unset( $params['page'] );
        
        foreach( $params as $key => $value )
            $url .= '/'.$key.'/'.$value;
        
        return $url;
    }
}

?>
