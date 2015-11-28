<?php

namespace Whathood\Mapper;

trait MapperTrait {

    protected $_mappers = null;

    public function addMapper($key, $mapper) {
        if (! $this->_mappers)
            $this->_mappers = array();
        $this->_mappers[$key] = $mapper;
    }

    protected function getMapper($key) {
        if ( ! array_key_exists($key, $this->getMappers()) ) {
            throw new \Exception("key $key not found in (".join(' ,', $this->getKeys()).")");
        }
        return $this->getMappers()[$key];
    }

    private function getMappers() {
        if (empty($this->_mappers))
            $this->_mappers = array();
        return $this->_mappers;
    }

    private function getKeys() {
        return array_keys($this->getMappers());
    }
}
