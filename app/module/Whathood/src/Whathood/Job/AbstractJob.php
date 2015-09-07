<?php
namespace Whathood\Job;

abstract class AbstractJob extends \SlmQueue\Job\AbstractJob {

    protected $_logger = null;

    public function setLogger($logger) {
        $this->_logger = $logger;
    }

    public function logger() { return $this->_logger; }

    public function __construct(array $data) {
        $hydrator = new \Zend\Stdlib\Hydrator\ClassMethods(true);
        $hydrator->hydrate($data,$this);
    }
}
