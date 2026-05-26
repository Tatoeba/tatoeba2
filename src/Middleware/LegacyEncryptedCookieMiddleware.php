<?php

namespace App\Middleware;

use Cake\Http\Middleware\EncryptedCookieMiddleware;

class LegacyEncryptedCookieMiddleware extends EncryptedCookieMiddleware
{
    private string $legacyKey;

    public function __construct(array $cookieNames, string $key, string $legacyKey, string $cipherType = 'aes')
    {
        parent::__construct($cookieNames, $key, $cipherType);
        $this->legacyKey = $legacyKey;
    }

    protected function _decode(string $value, $encrypt, ?string $key)
    {
        $decoded = parent::_decode($value, $encrypt, $key);
        if ($decoded === '') {
            return parent::_decode($value, $encrypt, $this->legacyKey);
        } else {
            return $decoded;
        }
    }
}
