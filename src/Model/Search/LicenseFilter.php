<?php

namespace App\Model\Search;

use App\Model\Exception\InvalidValueException;
use App\Lib\Licenses;

class LicenseFilter extends SearchFilter {
    const LICENSING_ISSUE = 'PROBLEM';

    protected function getAttributeName() {
        return 'license_id';
    }

    private function getValues() {
        $validLicenses = array_keys(Licenses::getSentenceLicenses());
        $validLicenses[0] = self::LICENSING_ISSUE;
        return $validLicenses;
    }

    public function __construct() {
        $this->setInvalidValueHandler(function($invalidValue) {
            throw new InvalidValueException("Value must be one of: ".implode(', ', $this->getValues()));
        });
    }

    public function and() {
        throw new \App\Model\Exception\InvalidAndOperatorException();
    }

    public function getValuesMap() {
        return array_flip($this->getValues());
    }
}
