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


/**
 * Model behavior for murmurhash3 helper functions.
 */
App::import('Lib', 'Murmurhash3');

class HashableBehavior extends ModelBehavior
{
    /**
     * Hash lang and text using murmurhash3.
     *
     * @param  object $model Model.
     * @param  string $lang  Item language.
     * @param  string $text  Item text.
     *
     * @return string
     */
    public function makeHash($model, $lang, $text)
    {
        $hash = murmurhash3_int($lang.$text);

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
        $binary = $this->padHashBinary($model, $binary);

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
        $binary = $this->padHashBinary($model, $binary);

        $method = 'findAllBy'.$column;

        return $model->{$method}($binary);
    }

    /**
     * Convert a binary id to a padded binary id.
     *
     * @param  object $model  Model.
     * @param  string $binary Binary id value.
     *
     * @return string
     */
    public function padHashBinary($model, $binary)
    {
        $hex = bin2hex($binary);

        $hex = str_pad($hex, 32, 0);

        return hex2bin($hex);
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
}
