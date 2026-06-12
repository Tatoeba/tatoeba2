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

    public function offsetSet($offset, $value): void {
        $offset = ($this->keyMapper)($offset);
        if (is_null($offset)) {
            $this->container[] = $value;
        } else {
            $this->container[$offset] = $value;
        }
    }

    public function offsetExists($offset): bool {
        $offset = ($this->keyMapper)($offset);
        return isset($this->container[$offset]);
    }

    public function offsetUnset($offset): void {
        $offset = ($this->keyMapper)($offset);
        unset($this->container[$offset]);
    }

    public function offsetGet($offset): mixed {
        $offset = ($this->keyMapper)($offset);
        return isset($this->container[$offset]) ? $this->container[$offset] : null;
    }
}

