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
        return new ViewModel();
	}

    public function navigationAction() {
        return new ViewModel();
    }

    public function aboutAction() {
        return new ViewModel();
    }
}

?>
