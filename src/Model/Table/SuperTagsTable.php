<?php
/**
 * Tatoeba Project, free collaborative creation of multilingual corpuses project
 * Copyright (C) 2010 SIMON   Allan   <allan.simon@supinfo.com>
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

use App\Model\CurrentUser;
use Cake\Database\Schema\TableSchema;
use Cake\ORM\Table;
use Cake\Event\Event;

class SuperTagsTable extends Table
{
    public function initialize(array $config) 
    {
        $this->hasMany('TagsSuperTags');
        $this->belongsToMany('SuperTags');

        $this->belongsTo('Users');
        $this->addBehavior('Autocompletable', [
            'fields' => ['name', 'id'],
            'order' => [],
        ]);
    }

    /**
     * Select a supertag
     *
     * @param String    $name
     *
     * @return Integer
     */
    public function getIdFromName($name) {
        $result = $this->find('all')
            ->where(['name' => $name])
            ->select(['id'])
            ->first();
            
        return $result ? $result->id : null;
    }

    /**
     * Add a supertag
     *
     * @param String    $name
     * @param String    $description
     * @param int       $userId
     *
     * @return bool
     */
    public function create($name, $description, $userId)
    {
        if (!empty($name)){
            $exists = $this->find('all')
                ->where(['name' => $name])
                ->count();
            if ($exists > 0)
                return false;

            $data = $this->newEntity([
                'name' => $name,
                'description' => $description,
                'user_id' => $userId
            ]);
            $added = $this->save($data);
            return $added;
        }
        else
            return false;
        
    }

    /**
     * Remove a supertag
     *
     * @param int   $id
     */
    public function remove($superTagId)
    {
        // check whether the supertag has no children
        $children = $this->TagsSuperTags->find('all')
            ->where(['parent' => $superTagId])
            ->count();
        if ($children == 0){
            $this->TagsSuperTags->deleteAll(['child' => $superTagId, 'child_type' => 'superTag']);
            $this->deleteAll(['id' => $superTagId]);
        }
        
        return ($children == 0);
    }
}
