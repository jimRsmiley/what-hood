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
        $env = getenv("APPLICATION_ENV");
        if (!$env)
            $env = 'production';
        return $env;
    }

    public static function is_production() {
      return static::environment() == 'production';
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

