<?php

namespace Whathood\Controller\Console;

use Whathood\Controller\BaseController;

class MemcachedController extends BaseController
{
    public function flushAction() {
        $memcached = $this->getServiceLocator()->get('Whathood\Service\Caching');
        $memcached->flush();
        return "memcached flushed";
    }
}
