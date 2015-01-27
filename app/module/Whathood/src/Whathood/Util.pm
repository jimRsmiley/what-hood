<?php

namespace Whathood;

class Util {

	public static function getHostname() {
		return gethostname();
	}

	public static function is_production() {
		return static::getHostname() == 'market.phl.io';
	}
}

