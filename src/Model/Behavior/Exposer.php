<?php
/**
    Tatoeba Project, free collaborative creation of languages corpuses project
    Copyright (C) 2026  Gilles Bedel

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU Affero General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU Affero General Public License for more details.

    You should have received a copy of the GNU Affero General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
namespace App\Model\Behavior;

/*
 * Helper class to keep track of what visible fields and what containments
 * have been set on a query. An instance of it is passed on as a query option
 * down to every containment setup by ExposedOnApiBehavior::findContainOnApi().
 */
class Exposer {
    private $contain = [];
    private $fields = [];

    public function in(string $assoc): Exposer {
        if (!isset($this->contain[$assoc])) {
            $this->contain[$assoc] = new Exposer();
        }
        return $this->contain[$assoc];
    }

    public function getContain(string $assoc): ?Exposer {
        return $this->contain[$assoc] ?? null;
    }

    public function addFields(array $fields) {
        $this->fields = array_merge($this->fields, $fields);
    }

    public function getFields(): array {
        return array_merge($this->fields, array_keys($this->contain));
    }
}
