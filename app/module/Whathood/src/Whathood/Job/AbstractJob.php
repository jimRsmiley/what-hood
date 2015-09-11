<?php
namespace Whathood\Job;

abstract class AbstractJob extends \SlmQueue\Job\AbstractJob {

    private $_logger = null;
    protected $_name = null;
    protected $_mapperBuilder;

    public static function build(array $data) {
        $obj = new static($data);
        if (!$obj->logger())
            throw new \InvalidArgumentException("logger may not be empty");

        if (!$obj->m())
            throw new \InvalidArgumentException("mapperBuilder may not be empty");
        return $obj;
    }

    public function __construct(array $data) {
        $hydrator = new \Zend\Stdlib\Hydrator\ClassMethods(true);
        $hydrator->hydrate($data,$this);
    }

    public function m() {
        return $this->_mapperBuilder;
    }

    public function setMapperBuilder($mapperBuilder) {
        $this->_mapperBuilder = $mapperBuilder;
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

    public function throwException($str) {
        $this->infoLog($str);
        throw new \Exception($str);
    }

    public function infoLog($str) {
        $str = "MessageQueue: $str";
        if ($this->logger())
            $this->logger()->info($str);
        print "$str\n";
    }

    private function logger() { return $this->_logger; }
}
