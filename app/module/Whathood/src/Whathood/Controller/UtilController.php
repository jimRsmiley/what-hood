<?php
namespace Whathood\Controller;

use Zend\View\Model\ViewModel;

/**
 * Description of RegionController
 *
 * @author Jim Smiley twitter:@jimRsmiley
 */
class UtilController extends BaseController {

    public function whatsMyIpAction() {
        return new ViewModel( array(
            'user_ip_address' => $this->getRequest()->getServer()->get('REMOTE_ADDRESS')
        ));
	}

    public function phpInfoAction() {
        die(phpinfo());
    }

    public function testExceptionLoggingAction() {
        throw new \Exception(
            'this is a test in IndexController\testExceptionLogging');
    }
}

?>
