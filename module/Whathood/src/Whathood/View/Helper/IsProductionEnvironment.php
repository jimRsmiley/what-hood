<?php
namespace Whathood\View\Helper;

use Zend\ServiceManager\ServiceManager;
/**
 * Description of LoggedInHelper
 *
 * @author Jim Smiley twitter:@jimRsmiley
 */
class IsProductionEnvironment extends \Zend\View\Helper\AbstractHelper {
    
    public function __invoke() {
        return ( getenv('APPLICATION_ENV') != 'development' );
    }
    
}

?>
