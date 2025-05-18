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
namespace App\Model\Table;

use Cake\ORM\Table;

class CategoriesTreeTable extends Table
{
    public function initialize(array $config) 
    {
        $this->hasMany('Tags')->setForeignKey('category_id');
        
        $this->addBehavior('Tree');
        $this->addBehavior('Autocompletable', [
            'order' => ['name']
        ]);
    }

    /**
     * Add a tag category
     *
     * @param String    $name
     * @param String    $description
     * @param Integer   $parentId
     *
     * @return bool
     */
    public function createOrEdit($name, $description, $parentName)
    {
        if (empty($name))
            return false;
        else {
            $data = $this->newEntity([
                'name' => $name,
                'description' => $description,
                'parent_id' => $this->getIdFromName($parentName)
            ]);

            $query = $this->find('all')->select(['id'])->where(['name' => $name]);
            if (!$query->isEmpty()) {
                $id = $query->first()['id'];
                $data->set('id', $id);
            }
            return $this->save($data);
        }
        
    }

    /**
     * Remove a tag category
     *
     * @param Integer   $categoryId
     */
    public function remove($categoryId)
    {
        // check this category contains no tag
        if ($this->Tags->exists(['category_id' => $categoryId]))
            return false;

        // check this category contains no other category
        if ($this->exists(['parent_id' => $categoryId]))
            return false;
        
        return $this->deleteAll(['id' => $categoryId]);   
    }

    public function getIdFromName($name) {
        $result = $this->find('all')
            ->where(['name' => $name])
            ->select(['id'])
            ->first();

        return $result ? $result->id : null;
    }

}
