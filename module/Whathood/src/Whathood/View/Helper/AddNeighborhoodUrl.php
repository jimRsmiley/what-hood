<?php
namespace Application\View\Helper;

use Application\Entity\Neighborhood;
/**
 * Description of UserRegionUrlHelper
 *
 * @author Jim Smiley twitter:@jimRsmiley
 */
class UserRegionUrlHelper extends \Zend\View\Helper\AbstractHelper {
    
    protected $root = '/application/neighborhood/by-id';
    
    public function __invoke( Neighborhood $n ) {
       
        $url = $this->root 
                . '?id=' . $n->getId();
        return $url;
    }
    
}

?>
