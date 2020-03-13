<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

class Audio extends Entity
{
    protected $_virtual = [
        'author',
    ];

    public static $defaultExternal = array(
        'username' => null,
        'license' => null,
        'attribution_url' => null,
    );

    protected function _getExternal($external) {
        if (is_array($external)) {
            $external = array_merge(self::$defaultExternal, $external);
            $external = array_intersect_key($external, self::$defaultExternal);
        }
        return $external;
    }

    protected function _setExternal($external) {
        $existingExternal = $this->external;
        if (is_array($this->external) && is_array($existingExternal)) {
            $external = array_merge($existingExternal, $external);
        }
        return $external;
    }

    protected function _getAuthor() {
        if ($this->user && $this->user->username) {
            return $this->user->username;
        } else {
            return $this->external['username'];
        }
    }
}
