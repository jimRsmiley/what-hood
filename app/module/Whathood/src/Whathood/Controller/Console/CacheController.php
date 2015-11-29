<?php

namespace Whathood\Controller\Console;

use Whathood\Controller\BaseController;

class CacheController extends BaseController
{
    public function flushAction() {
        $cache = $this->getServiceLocator()->get('Whathood\Service\Caching');
        $cache->flush();
        return "cache flushed\n";
    }
}
