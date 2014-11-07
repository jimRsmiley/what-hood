<?php
namespace Whathood\View\Helper;

use Whathood\Entity\Neighborhood;
/**
 * Description of UserRegionUrlHelper
 *
 * @author Jim Smiley twitter:@jimRsmiley
 */
class asdfUserRegionUrlHelper extends \Zend\View\Helper\AbstractHelper {
    
    protected $root = '/whathood/neighborhood/by-id';
    
    public function __invoke( Neighborhood $n ) {
       
        $url = $this->root 
                . '?id=' . $n->getId();
        return $url;
    }
    
}

?>
