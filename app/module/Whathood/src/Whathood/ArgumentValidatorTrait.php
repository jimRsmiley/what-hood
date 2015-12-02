<?php

namespace Whathood;

trait ArgumentValidatorTrait {

    public static function validateNotEmpty($val, $valName) {
        if (empty($val))
            throw new \InvalidArgumentException("value $valName may not be empty");
    }
}
