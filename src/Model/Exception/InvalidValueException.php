<?php

namespace App\Model\Exception;

use Exception;

class InvalidValueException extends Exception
{
    protected $thrower;

    public function __construct() {
        $args = func_get_args();
        $this->thrower = array_shift($args);
        parent::__construct(...$args);
    }

    public function getThrower() {
        return $this->thrower;
    }
}
