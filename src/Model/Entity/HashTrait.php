<?php
namespace App\Model\Entity;

use App\Lib\Murmurhash3;

/**
 * Adds hashing feature to an entity
 *
 * An entity needs to call 'setHashFields' in order to set the fields the hash
 * is based on and then 'updateHash' whenever the hash should be recalculated.
 **/
trait HashTrait
{
    /**
     * The current hash value
     *
     * @var string|null
     **/
    private $_hash = null;

    /**
     * The fields the hash is based on
     *
     * All the fields will be concatenated in the order of appearance in this array.
     *
     * @var array
     **/
    private $_hashFields = [];

    /**
     * Setter method
     *
     * @param string $value New value for the hash
     *
     * @return string
     **/
    public function _setHash($value) {
        $this->_hash = $value;
        return $value;
    }

    /**
     * Getter method
     *
     * Returns the cached value or recalculates the hash if needed.
     *
     * @return string
     **/
    public function _getHash() {
        if ($this->_hash === null) {
            $str = array_reduce(
                $this->_hashFields,
                function ($carry, $item) {
                    return $carry . $this->get($item);
                },
                ''
            );
            $this->_hash = $this->makeHash($str);
        }
        return $this->_hash;
    }

    /**
     * Clears the current hash and marks it as dirty
     *
     * The hash will be recalculated at the next read access.
     *
     * @return void
     **/
    public function updateHash() {
        $this->_hash = null;
        $this->setDirty('hash');
    }

    /**
     * Sets the fields for hashing
     *
     * Should be called by the entity.
     *
     * @var array $fields
     *
     * @return void
     **/
    public function setHashFields($fields) {
        $this->_hashFields = $fields;
    }

    /**
     * Create hash from given string
     *
     * The hash will be 16 characters long (padded with NUL
         * characters if necessary).
     *
     * @param  string $str
     *
     * @return string
     */
    private function makeHash($string) {
        $hash = Murmurhash3::murmurhash3($string);
        return str_pad($hash, 16, "\0");
    }
}
