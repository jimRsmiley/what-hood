<?php
namespace Whathood\Job;

abstract class AbstractJob extends \SlmQueue\Job\AbstractJob {

    protected $_logger = null;
    protected $_name = null;

    public function __construct(array $data) {
        $hydrator = new \Zend\Stdlib\Hydrator\ClassMethods(true);
        $hydrator->hydrate($data,$this);
    }

    public function getName() {
        if (!$this->_name) {
            $reflect = new \ReflectionClass($this);
            $this->_name = strtolower($reflect->getShortName());
        }
        return $this->_name;
    }

    public function setLogger($logger) {
        $this->_logger = $logger;
    }

    public function infoLog($str) {
        print "$str\n";
    }

    public function logger() { return $this->_logger; }

}
