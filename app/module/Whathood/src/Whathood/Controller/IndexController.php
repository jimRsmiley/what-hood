<?php
namespace Whathood\Controller;

use Zend\View\Model\ViewModel;

/**
 * Description of RegionController
 *
 * @author Jim Smiley twitter:@jimRsmiley
 */
class IndexController extends BaseController {

    public function indexAction() {
        $regions = $this->regionMapper()->fetchDistinctRegionNames();

        return new ViewModel( array( 'regionNames' => $regions ) );
	}

    public function navigationAction() {
        return new ViewModel();
    }

    public function aboutAction() {
        return new ViewModel();
    }

    public function testExceptionLoggingAction() {
        throw new \Exception(
            'this is a test in IndexController\testExceptionLogging');
    }
}

?>
