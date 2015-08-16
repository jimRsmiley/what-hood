<?php

namespace Whathood;

class Util {

	public static function getHostname() {
		return gethostname();
	}

    public static function memory_usage() {
        $bytes = memory_get_usage();
        $kbytes = $bytes / 1024;
        $mbytes = $kbytes / 1024;
        return round($mbytes,3);
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

    public static function getRemoteIp($request) {
        $server = $request->getServer();

        if ($server->get("HTTP_X_REAL_IP")) {
            return $server->get("HTTP_X_REAL_IP");
        }
        else {
            return $server->get("REMOTE_ADDR");
        }
    }
}

