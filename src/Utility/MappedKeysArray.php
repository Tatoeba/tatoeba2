<?php

namespace App\Utility;

use ArrayAccess;

class MappedKeysArray implements ArrayAccess
{
    private $container = [];
    private $keyMapper;

    public function __construct(callable $keyMapper, array $init = []) {
        $this->keyMapper = $keyMapper;
        foreach ($init as $key => $value) {
            $this->offsetSet($key, $value);
        }
    }

    public function offsetSet($offset, $value) {
        $offset = ($this->keyMapper)($offset);
        if (is_null($offset)) {
            $this->container[] = $value;
        } else {
            $this->container[$offset] = $value;
        }
    }

    public function offsetExists($offset) {
        $offset = ($this->keyMapper)($offset);
        return isset($this->container[$offset]);
    }

    public function offsetUnset($offset) {
        $offset = ($this->keyMapper)($offset);
        unset($this->container[$offset]);
    }

    public function offsetGet($offset) {
        $offset = ($this->keyMapper)($offset);
        return isset($this->container[$offset]) ? $this->container[$offset] : null;
    }
}

