<?php
namespace Whathood\View\Helper;

use Zend\ServiceManager\ServiceManager;
/**
 * Description of LoggedInHelper
 *
 * @author Jim Smiley twitter:@jimRsmiley
 */
class IsAdministrator extends \Zend\View\Helper\AbstractHelper {
    public function __invoke() {
		return false;
    }
}

?>
