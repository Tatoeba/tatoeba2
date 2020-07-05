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

class TagsLinksTable extends Table
{
    public function initialize(array $config) 
    {
        // $this->hasMany('Tags');
        $this->belongsToMany('Tags');
        $this->belongsTo('Users');
        $this->addBehavior('Timestamp');
    }

    /**
     * Add a link
     *
     * @param String   $parent
     * @param String   $child
     * @param int   $userId
     *
     * @return bool
     */
    public function addLink($parent, $child, $userId)
    {
        // parent and child must reference the Tag database
        $id_parent = $this->Tags->getIdFromName($parent);
        $id_child = $this->Tags->getIdFromName($child);
        if ($id_parent == null || $id_child == null){
            return false;
        }
        // parent and child must be different
        if ($id_parent == $id_child)
            return false;
        
        // prevent cycles: there must not exist a path from $child to $parent
        $candidates = array($id_child);
        $cycle = false;
        while (!$cycle && count($candidates)) {
            $new_candidates = array();
            foreach ($this->find('all')->where(['parent IN' => $candidates])->select(['child']) as $key => $value){
                array_push($new_candidates, $value['child']);
            }
            $candidates = $new_candidates;
            $cycle = in_array($id_parent, $candidates);
        }
        if ($cycle)
            return false;

        $data = $this->newEntity([
            'parent' => $id_parent,
            'child' => $id_child,
            'user_id' => $userId
        ]);
        $added = $this->save($data);
        return $added;
    }

    /**
     * Remove a link
     *
     * @param int   $parent
     * @param int   $child
     */
    public function removeLink($parent, $child)
    {
        $this->deleteAll([
            'parent' => $parent,
            'child' => $child
        ]);
    }
}
