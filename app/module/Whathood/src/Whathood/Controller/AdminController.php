<?php
namespace Whathood\Controller;

class AdminController extends BaseController {

    public function indexAction() {
	if (!$this->isAllowed('admin')) {
	    throw new \BjyAuthorize\Exception\UnAuthorizedException('Grow a beard first!');
	}	    
    
    }
}
