<?php

namespace Whathood;

class Collection {

    protected $objects;

    public function getObjects() {
        return $this->objects;
    }

    public function setObjects(array $objects) {
        $this->objects = $objects;
    }

    public function __construct(array $data=null) {
        if(!empty($data))
            $this->setObjects($data);
    }

    public function __toString() {
        $str = "";
        foreach($this->getObjects() as $object) {
            $str .= $object->__toString()."\n";
        }
        return $str;
    }
}
