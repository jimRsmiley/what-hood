<?php

namespace Whathood;

use Zend\Session\Storage\ArrayStorage;
use Zend\Session\SessionManager;
use Zend\Session\Container;

class Timer {

    protected $_description;

    protected $_start_time;

    // private constructor
    private function __construct(array $data) {
        $this->_description = $data['description'];
    }

    public static function start($timer_str) {
        if (!$timer_str)
            throw new \InvalidArgumentException("timer_str must be defined");
        $t = new static(array('description'=>$timer_str));
        $t->setStartTime(microtime(true));
        return $t;
    }

    protected function setStartTime($start_time) {
        $this->_start_time = $start_time;
    }

    public function stop() {
        $sessionTimers = new Container('timers');
        $description = $this->_description;
        $sessionTimers->$description = microtime(true) - $this->_start_time;
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

    public function elapsedReadableString() {
        $elapsed_milli= $this->elapsed_milliseconds();
        if ($elapsed_milli < 1000)
            return sprintf("%sms",$elapsed_milli);
        else {
            $elapsed_seconds = $elapsed_milli / 1000;
            if ($elapsed_seconds < 60)
                return sprintf("%ssecs",$elapsed_seconds);
            else {
                $elapsed_minutes = $elapsed_seconds / 60;
                return sprintf("%smins",$elapsed_minutes);
            }
        }
    }

    /**
     * return a string for all timers in the session store
     *
     * @return string
     */
    public static function report_str() {
        $sessionTimers = new Container('timers');

        \Zend\Debug\Debug::dump($sessionTimers);
        exit;
    }

}
