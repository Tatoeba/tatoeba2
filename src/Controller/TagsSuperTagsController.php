<?php
/**
 * Tatoeba Project, free collaborative creation of multilingual corpuses project
 * Copyright (C) 2010 Allan SIMON <allan.simon@supinfo.com>
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
namespace App\Controller;

use App\Controller\AppController;
use Cake\Event\Event;
use App\Model\CurrentUser;


class TagsSuperTagsController extends AppController
{
    /**
     * Controller name
     *
     * @var string
     * @access public
     */
    public $name = 'TagsSuperTags';
    public $components = ['CommonSentence', 'Flash'];
    public $helpers = ['Pagination'];

    public function manage(){
        $all_tags = [];
        $this->loadModel('Tags');
        foreach ($this->Tags->find('all')->select(['id', 'name', 'nbrOfSentences']) as $key => $value)
            $all_tags[$value['id']] = [
                'name' => $value['name'], 
                'nbrOfSentences' => $value['nbrOfSentences']
            ];

        $all_superTags = [];
        $all_tagsLinks = [];
        $all_superTagsLinks = [];
        $this->loadModel('SuperTags');
        foreach ($this->SuperTags->find('all')->select(['id', 'name']) as $value) {
            $superTagId = $value['id'];
            $all_superTags[$superTagId] = $value['name'];
            $all_tagsLinks[$superTagId] = [];
            $all_superTagsLinks[$superTagId] = [];
        }

        $remaining_links = 0;
        $non_leaves = [];
        foreach ($this->TagsSuperTags->find('all')->select(['parent', 'child', 'child_type']) as $value) {
            if ($value['child_type'] == 'tag')
                array_push($all_tagsLinks[$value['parent']], $value['child']);
            else {
                array_push($all_superTagsLinks[$value['child']], $value['parent']);
                $non_leaves[$value['parent']] = 1;
                $remaining_links += 1;
            }
        }

        $tree = [];
        $leaves = array_diff(array_keys($all_superTags), array_keys($non_leaves));
        foreach ($leaves as $leave)
            $tree[$leave] = [];
        
        while ($remaining_links) {
            foreach ($all_superTagsLinks as $child => $parents) {
                if (count($parents) && array_key_exists($child, $tree)){
                    // 1. copy
                    $subtree = $tree[$child];
                    // 2. remove
                    unset($tree[$child]);
                    // 3. replace
                    foreach ($parents as $parent) {
                        if (!array_key_exists($parent, $tree))
                            $tree[$parent] = [];
                        $tree[$parent][$child] = $subtree;
                    }
                    // 4. remove used links
                    $remaining_links -= count($parents);
                    $all_superTagsLinks[$child] = [];
                }
            }
        }

        $this->set('all_tags', $all_tags);
        $this->set('all_super_tags', $all_superTags);
        $this->set('all_tags_links', $all_tagsLinks);
        $this->set('all_super_tags_links', $tree);
    }

    public function createTagSuperTag(){
        $parent = $this->request->data('parent');
        $child = $this->request->data('child');
        $childType = ($this->request->data('childType') == 0) ? 'tag' : 'superTag';
        $userId = CurrentUser::get('id');

        $added = $this->TagsSuperTags->create($parent, $child, $childType, $userId);

        return $this->redirect([
            'controller' => 'tags_super_tags',
            'action' => 'manage',
            '?' => ['tagSuperTagAdded' => $added],
        ]);
    }

    public function removeTagSuperTag($parent, $child, $childType){
        $this->TagsSuperTags->remove($parent, $child, $childType);
        
        return $this->redirect([
            'controller' => 'tags_super_tags',
            'action' => 'manage',
        ]);
    }
}