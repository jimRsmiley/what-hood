<?php

namespace Whathood;

class Util {

	public static function getHostname() {
		return gethostname();
	}

    public static function memory_usage() {
        $bytes = memory_get_usage();
        $kbytes = $bytes / 1024;
        return $kbytes;
    }

    public static function environment() {
        if (static::is_production())
            return "production";
        else
            return getenv("APPLICATION_ENV");
    }

	public static function is_production() {
		return static::getHostname() == 'market.phl.io';
	}

    public static function prompt_user($msg) {
        print $msg;
        $handle = fopen("php://stdin",'r');
        $line = fgets($handle);
    }
}

