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
        $remote_ip = \Whathood\Util::getRemoteIp(
                $this->getRequest() );

        return new ViewModel(array(
            'remoteIp' => $remote_ip ));
	}

    public function testQueueAction() {
        $queue = $this->queue('message_queue')
            ->push('Whathood\Job\EmailJob',array('subject' => 'My Test Subject',
                'body' => 'My test body'));
    }

    public function emptyAction() {}

    public function phpinfoAction() {
        die(phpinfo());
    }

    public function testExceptionAction() {
        throw new \Exception(
            'this is a test in IndexController\testExceptionLogging');
    }
}

?>
