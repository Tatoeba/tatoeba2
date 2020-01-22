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

    /*+
     * Flag for updating the hash
     *
     * @var boolean
     **/
    private $_needsUpdate = false;

    /**
     * The fields the hash is based on
     *
     * All the fields will be concatenated in the order of appearance in this array.
     *
     * @var array
     **/
    private $_hashFields = [];

    /**
     * Initializes the hash
     *
     * Should be called by entity's constructor.
     *
     * @param string|null $hash   Initial value of the hash
     * @param array       $fields Fields the hash is based on
     *
     * @return void
     **/
    public function initializeHash($hash, $fields) {
        $this->_hash = $hash;
        $this->_hashFields = $fields;
        $this->_needsUpdate = false;
    }

    /**
     * Setter method
     *
     * @param string $value New value for the hash
     *
     * @return string
     **/
    public function _setHash($value) {
        $this->_hash = $value;
        $this->_needsUpdate = false;
        return $value;
    }

    /**
     * Getter method
     *
     * Returns the current value or recalculates the hash if needed.
     *
     * @return string
     **/
    public function _getHash() {
        if ($this->_needsUpdate) {
            $str = array_reduce(
                $this->_hashFields,
                function ($carry, $item) {
                    return $carry . $this->get($item);
                },
                ''
            );
            $this->_hash = $this->makeHash($str);
            $this->_needsUpdate = false;
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
        $this->_needsUpdate = true;
        $this->setDirty('hash');
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
