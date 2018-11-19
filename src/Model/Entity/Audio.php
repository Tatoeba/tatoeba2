<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

class Audio extends Entity
{
    public static $defaultExternal = array(
        'username' => null,
        'license' => null,
        'attribution_url' => null,
    );

    protected function _getExternal($external) {
        $external = array_merge(self::$defaultExternal, (array)$external);
        $external = array_intersect_key($external, self::$defaultExternal);
        return $external;
    }

    protected function _setExternal($external) {
        $existingExternal = (array)$this->external;
        $external = array_merge($existingExternal, $external);
        return $external;
    }
}
