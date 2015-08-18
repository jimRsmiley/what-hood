<?php

namespace Whathood\Service;

use Zend\Cache\StorageFactory;

class CachingService {

    protected $_cache;

    public function __construct(array $data = null) {
        $hydrator = new \Zend\Stdlib\Hydrator\ClassMethods();
        $hydrator->hydrate($data, $this);
    }

    public function getRegionGeoJson($region) {
        $key    = 'region-geojson-'.$region->getId();
        $result = $cache->getItem($key, $success);
        if (!$success) {
            $result = doExpensiveStuff();
            $cache->setItem($key, $result);
        }
    }

    public function storeRegionGeoJson($geojson_str) {
    }

}
