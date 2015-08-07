<?php
namespace Whathood\View\Paginator;
/**
 * Description of NeighborhoodPaginator
 *
 * @author Jim Smiley twitter:@jimRsmiley
 */
class UserPolygonPaginator extends \Zend\Paginator\Paginator {

	protected $_base_url;
    protected $uriParams;

	public function getBaseUrl() {
		return $this->_base_url;
	}

	public function setBaseUrl($base_url) {
		$this->_base_url = $base_url;
	}

    public function setUriParams( $array ) {
        $this->uriParams = $array;
    }

    public function pageUrl($pageNum) {
        $this->uriParams['page'] = $pageNum;
        return $this->getPageUrl();
    }

    public function getPageUrl() {
        $url = $this->getBaseUrl();

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
