<?php
/**
 * Tatoeba Project, free collaborative creation of languages corpuses project
 * Copyright (C) 2015  Gilles Bedel
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
namespace App\Model\Behavior;

use Cake\ORM\Behavior;

/**
 * Model behavior for murmurhash3 helper functions.
 */
class HashableBehavior extends Behavior
{
    /**
     * Hash lang and text using murmurhash3.
     *
     * @param  string $lang  Item language.
     * @param  string $text  Item text.
     *
     * @return string
     */
    public function makeHash($lang, $text)
    {
        $hash = $this->murmurhash3_int($lang.$text);

        return base_convert(sprintf("%u\n", $hash), 10, 32);
    }

    /**
     * Find a record by binary hash value.
     *
     * @param  object $model  Model.
     * @param  string $binary Binary id value.
     * @param  string $column Column on table to search.
     *
     * @return array|boolean
     */
    public function findByBinary($model, $binary, $column)
    {
        $binary = $this->padHashBinary($binary);

        return $model->find([$column => $binary]);
    }

    /**
     * Find all records by binary hash value.
     *
     * @param  object $model  Model
     * @param  string $binary Binary id value.
     * @param  string $column Column on table to search.
     *
     * @return array
     */
    public function findAllByBinary($model, $binary, $column)
    {
        $binary = $this->padHashBinary($binary);

        $method = 'findAllBy'.$column;

        return $model->{$method}($binary);
    }

    /**
     * Convert a binary id to a padded binary id.
     *
     * @param  string $binary Binary id value.
     *
     * @return string
     */
    public function padHashBinary($binary)
    {
        $hex = bin2hex($binary);

        $hex = str_pad($hex, 32, 0);

        return $hex;
    }

    /**
     * Return true if item is true duplicate.
     *
     * @param  object $model Model.
     * @param  string $text  Item text.
     * @param  string $lang  Item language.
     * @param  array  $item  Item to be compared against.
     *
     * @return bool
     */
    public function confirmDuplicate($model, $text, $lang, $item)
    {
        $itemText = $item['text'];

        $itemLang = $item['lang'];

        if ($itemText === $text && $itemLang === $lang) {
            return true;
        }

        return false;
    }


    /**
     * PHP Implementation of MurmurHash3
     *
     * @author Stefano Azzolini (lastguest@gmail.com)
     * @see https://github.com/lastguest/murmurhash-php
     * @author Gary Court (gary.court@gmail.com)
     * @see http://github.com/garycourt/murmurhash-js
     * @author Austin Appleby (aappleby@gmail.com)
     * @see http://sites.google.com/site/murmurhash/
     *
     * @param  string $key   Text to hash.
     * @param  number $seed  Positive integer only
     * @return number 32-bit (base 32 converted) positive integer hash
     */
    private function murmurhash3_int($key,$seed=0){
        $key  = array_values(unpack('C*',(string) $key));
        $klen = count($key);
        $h1   = (int)$seed;
        for ($i=0,$bytes=$klen-($remainder=$klen&3) ; $i<$bytes ; ) {
            $k1 = $key[$i]
            | ($key[++$i] << 8)
            | ($key[++$i] << 16)
            | ($key[++$i] << 24);
            ++$i;
            $k1  = (((($k1 & 0xffff) * 0xcc9e2d51) + ((((($k1 >= 0 ? $k1 >> 16 : (($k1 & 0x7fffffff) >> 16) | 0x8000)) * 0xcc9e2d51) & 0xffff) << 16))) & 0xffffffff;
            $k1  = $k1 << 15 | ($k1 >= 0 ? $k1 >> 17 : (($k1 & 0x7fffffff) >> 17) | 0x4000);
            $k1  = (((($k1 & 0xffff) * 0x1b873593) + ((((($k1 >= 0 ? $k1 >> 16 : (($k1 & 0x7fffffff) >> 16) | 0x8000)) * 0x1b873593) & 0xffff) << 16))) & 0xffffffff;
            $h1 ^= $k1;
            $h1  = $h1 << 13 | ($h1 >= 0 ? $h1 >> 19 : (($h1 & 0x7fffffff) >> 19) | 0x1000);
            $h1b = (((($h1 & 0xffff) * 5) + ((((($h1 >= 0 ? $h1 >> 16 : (($h1 & 0x7fffffff) >> 16) | 0x8000)) * 5) & 0xffff) << 16))) & 0xffffffff;
            $h1  = ((($h1b & 0xffff) + 0x6b64) + ((((($h1b >= 0 ? $h1b >> 16 : (($h1b & 0x7fffffff) >> 16) | 0x8000)) + 0xe654) & 0xffff) << 16));
        }
        $k1 = 0;
        switch ($remainder) {
            case 3: $k1 ^= $key[$i + 2] << 16;
            case 2: $k1 ^= $key[$i + 1] << 8;
            case 1: $k1 ^= $key[$i];
            $k1  = ((($k1 & 0xffff) * 0xcc9e2d51) + ((((($k1 >= 0 ? $k1 >> 16 : (($k1 & 0x7fffffff) >> 16) | 0x8000)) * 0xcc9e2d51) & 0xffff) << 16)) & 0xffffffff;
            $k1  = $k1 << 15 | ($k1 >= 0 ? $k1 >> 17 : (($k1 & 0x7fffffff) >> 17) | 0x4000);
            $k1  = ((($k1 & 0xffff) * 0x1b873593) + ((((($k1 >= 0 ? $k1 >> 16 : (($k1 & 0x7fffffff) >> 16) | 0x8000)) * 0x1b873593) & 0xffff) << 16)) & 0xffffffff;
            $h1 ^= $k1;
        }
        $h1 ^= $klen;
        $h1 ^= ($h1 >= 0 ? $h1 >> 16 : (($h1 & 0x7fffffff) >> 16) | 0x8000);
        $h1  = ((($h1 & 0xffff) * 0x85ebca6b) + ((((($h1 >= 0 ? $h1 >> 16 : (($h1 & 0x7fffffff) >> 16) | 0x8000)) * 0x85ebca6b) & 0xffff) << 16)) & 0xffffffff;
        $h1 ^= ($h1 >= 0 ? $h1 >> 13 : (($h1 & 0x7fffffff) >> 13) | 0x40000);
        $h1  = (((($h1 & 0xffff) * 0xc2b2ae35) + ((((($h1 >= 0 ? $h1 >> 16 : (($h1 & 0x7fffffff) >> 16) | 0x8000)) * 0xc2b2ae35) & 0xffff) << 16))) & 0xffffffff;
        $h1 ^= ($h1 >= 0 ? $h1 >> 16 : (($h1 & 0x7fffffff) >> 16) | 0x8000);
        return $h1;
    }
      
    private function murmurhash3($key,$seed=0){
    return base_convert(murmurhash3_int($key,$seed),10,32);
    }
}
