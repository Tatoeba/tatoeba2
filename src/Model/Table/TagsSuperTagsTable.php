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
use Cake\Utility\ClassRegistry;

class TagsSuperTagsTable extends Table
{
    public function initialize(array $config) 
    {
        $this->belongsTo('Users');
        $this->belongsTo('Tags');
        $this->belongsTo('SuperTags');
    }

    /**
     * Add a link
     *
     * @param String    $parent
     * @param String    $child
     * @param String    $childType
     * @param int       $userId
     *
     * @return bool
     */
    public function create($parent, $child, $childType, $userId)
    {
        // parent must reference SuperTag and child must reference childType (tag || superTag)
        $idParent = $this->SuperTags->getIdFromName($parent);
        if ($childType == 'tag')
            $idChild = $this->Tags->getIdFromName($child);
        else
            $idChild = $this->SuperTags->getIdFromName($child);

        // both child and parent must be true references
        if ($idParent == null || $idChild == null)
            return false;

        // duplicates are not allowed
        $exists = $this->find('all')
            ->where([
                'parent' => $idParent,
                'child' => $idChild,
                'child_type' => $childType
            ])->count();
        if ($exists > 0)
            return false;

        if ($childType == 'superTag') {
            // child and parent must be different
            if ($idParent == $idChild)
                return false;

            // there must not already exist a path from child to parent
            $candidates = array($idChild);
            $cycle = false;
            while (!$cycle && count($candidates)) {
                $new_candidates = array();
                $result = $this->find('all')
                    ->where([
                        'parent IN' => $candidates,
                        'child_type = ' => 'superTag'
                    ])
                    ->select(['child']);
                foreach ($result as $key => $value)
                    array_push($new_candidates, $value['child']);

                $candidates = $new_candidates;
                $cycle = in_array($idParent, $candidates);
            }
            if ($cycle)
                return false;
        }

        $data = $this->newEntity([
            'parent' => $idParent,
            'child' => $idChild,
            'child_type' => $childType,
            'user_id' => $userId
        ]);
        $added = $this->save($data);
        return $added;
    }

    /**
     * Remove a link
     *
     * @param int       $parent
     * @param int       $child
     * @param String    $childType
     */
    public function remove($parent, $child, $childType)
    {
        // if $childType == tag, detach child (tag) from parent (superTag)
        // else if $childType == superTag, detag child (superTag) from parent (superTag), along with all its children
        $this->deleteAll([
            'parent' => $parent,
            'child' => $child,
            'child_type' => $childType
        ]);
        return true;
    }
}
