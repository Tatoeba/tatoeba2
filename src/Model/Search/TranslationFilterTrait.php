<?php

namespace App\Model\Search;

trait TranslationFilterTrait {
    public function and() {
        throw new \App\Model\Exception\InvalidAndOperatorException();
    }

    protected function _compile() {
        $compiled = parent::_compile();
        if (count($compiled)) {
            list($key, $values, $exclude) = $compiled[0];
            $values = array_map(
                function ($value) use ($key) {
                    // Stupid but working input check; carefully expand if needed
                    if (is_string($value) && preg_match('/[^a-z]/', $value) == false) {
                        $value = "'$value'";
                    } elseif (!is_int($value)) {
                        throw new \RuntimeException("Invalid value used in ". self::class . "($key): " . var_export($value, true));
                    }
                    return "t.$key=$value";
                },
                $values
            );
            return $this->_join('|', $values, $exclude);
        } else {
            return null;
        }
    }
}

