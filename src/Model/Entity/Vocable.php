<?php
/**
 * Tatoeba Project, free collaborative creation of multilingual corpuses project
 * Copyright (C) 2020 Tatoeba Project
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
namespace App\Model\Entity;

use Cake\ORM\Entity;
use App\Model\Entity\HashTrait;

class Vocable extends Entity
{
    use HashTrait;

    public function __construct($properties = [], $options = []) {
        parent::__construct($properties, $options);
        $hash = $properties['hash'] ?? null;
        $this->initializeHash($hash, ['lang', 'text']);
    }

    protected function _setLang($value)
    {
        $this->updateHash();
        return $value;
    }

    protected function _setText($value)
    {
        $this->updateHash();
        return trim($value);
    }
}
