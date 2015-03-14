<?php

namespace Whathood;

class Timer {

    protected $_start_time;

    protected $_end_time;

    public function __construct() {}

    public function start() {
        $this->_start_time = microtime(true);
    }

    public function elapsed_milliseconds() {
        return round((microtime(true) - $this->_start_time)*1000,0);
    }

    public function elapsed_seconds() {
        return round(microtime(true) - $this->_start_time,1);
    }

    public function elapsed_minutes() {
        return round($this->elapsed_seconds()/60,2);
    }

    public static function init() {
        $t = new static();
        $t->start();
        return $t;
    }
}
