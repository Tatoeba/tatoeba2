<?php
/**
 * Tatoeba Project, free collaborative creation of languages corpuses project
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
namespace App\Model\Behavior;

use Cake\ORM\Behavior;
use Cake\Collection\Collection;
use Cake\ORM\Query;

/**
 * Behavior for handling duplicate entities
 *
 * This behavior adds the method 'SaveWithDuplicateCheck' to a table which checks for
 * duplicates before saving an entity. Searching for duplicates requires a
 * hash column in the table.
 */
class DuplicateBehavior extends Behavior
{
     /**
     * Default config
     *
     * hash     name of the hash column
     * fields   array containing the column names the hash is calculated from
     *
     * @var array
     */
    protected $_defaultConfig = [
        'implementedMethods' =>
            ['SaveWithDuplicateCheck' => 'SaveWithDuplicateCheck',
             'confirmDuplicate' => 'confirmDuplicate'],
        'hash' => 'hash',
        'fields' => ['text', 'lang']
    ];

    /**
     * Checks whether the entity is already in the database before saving
     *
     * If a duplicate is found it will be marked as such and returned. Otherwise
     * the given entity is saved as usual.
     *
     * @param Entity $entity  Entity which should be stored
     *
     * @return Entity|false  The returned entity will have an additional boolean property
     *                       'isDuplicate'.
     */
    public function SaveWithDuplicateCheck($entity) {
        $model = $this->getTable();
        $hashColumn = $this->config('hash');
        $hash = $entity->get($hashColumn);
        $candidates = $model->find('all')->where([$hashColumn => $hash]);
        foreach ($candidates as $candidate) {
            if ($this->confirmDuplicate($candidate, $entity)) {
                $candidate->isDuplicate = true;
                return $candidate;
            }
        }

        // There is no duplicate
        $saved = $model->save($entity);

        if ($saved) {
            $saved->isDuplicate = false;
        }

        return $saved;
    }

    /**
     * Checks wether two entities are duplicates
     *
     * They are considered as duplicates when all the fields used for hashing are equal
     *
     * @param  Entity $old
     * @param  Entity $new
     *
     * @return bool
     */
    public function confirmDuplicate($old, $new)
    {
        $collection = new Collection($this->config('fields'));
        return $collection->every(function ($field) use ($old, $new) {
            return $old->get($field) == $new->get($field);
        });
    }
}
