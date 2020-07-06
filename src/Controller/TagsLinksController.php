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
use App\Event\SuggestdListener;
use Cake\Event\Event;
use App\Model\CurrentUser;


class TagsLinksController extends AppController
{
    /**
     * Controller name
     *
     * @var string
     * @access public
     */
    public $name = 'TagsLinks';
    public $components = ['CommonSentence', 'Flash'];
    public $helpers = ['Pagination'];

    public function manage(){
        $all_tags = array();
        $links = array();
        $temp = array();
        $s = 0;
        $this->loadModel('Tags');
        foreach ($this->Tags->find('all')->select(['id', 'name', 'nbrOfSentences']) as $key => $value){
            $all_tags[$value['id']] = array($value['name'], $value['nbrOfSentences']);
            $links[$value['id']] = array();
            $temp[$value['id']] = array();
        }
        foreach ($this->TagsLinks->find('all')->select(['parent', 'child']) as $key => $value){
            $s += 1;
            array_push($links[$value['child']], $value['parent']);
            array_push($temp[$value['parent']], $value['child']);
        }

        $tree = array();
        foreach ($temp as $parent => $children){
            if (!count($children))
                $tree[$parent] = array();
        }
        while ($s) {
            foreach ($links as $child => $parents) {
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
                    $s -= count($parents);
                    $links[$child] = [];
                }
            }
        }

        $this->set('all_tags', $all_tags);
        $this->set('tree', $tree);
    }

    /**
     * Add a link
     *
     * @return void
     */

    public function add()
    {
        $parent = $this->request->data('parentTag');
        $child = $this->request->data('childTag');
        $userId = CurrentUser::get("id");
        $username = CurrentUser::get("username");
        $link = $this->TagsLinks->addLink($parent, $child, $userId);
        return $this->redirect([
            'controller' => 'tags_links',
            'action' => 'manage'
        ]);
    }

    /**
     * Remove a link
     *
     * @return void
     */

    public function remove($parent, $child)
    {
        $link = $this->TagsLinks->removeLink($parent, $child);
        return $this->redirect([
            'controller' => 'tags_links',
            'action' => 'manage'
        ]);
    }
}
