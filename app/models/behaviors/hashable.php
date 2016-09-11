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

/**
 * Model behavior for murmurhash3 helper functions.
 */
App::import('Vendor', 'murmurhash3');

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
        return murmurhash3($lang.$text);
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
}
