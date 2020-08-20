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


class CategoriesTreeController extends AppController
{
    /**
     * Controller name
     *
     * @var string
     * @access public
     */
    public $name = 'CategoriesTree';
    public $components = ['CommonSentence', 'Flash'];
    public $helpers = ['Pagination'];

    public function manage(){
        $tags = [];
        $this->loadModel('Tags');
        foreach ($this->Tags->find('all')->select(['id', 'name', 'nbrOfSentences', 'category_id']) as $value) {
            $category = ($value['category_id'] == null) ? -1 : $value['category_id'];
            if (!array_key_exists($category, $tags)) {
                $tags[$category] = [];
            }
            array_push($tags[$category], [
                'id' => $value['id'],
                'name' => $value['name'], 
                'nbrOfSentences' => $value['nbrOfSentences']
            ]);
            
        }

        $treeList = $this->CategoriesTree->find('treeList');

        $tree = [];
        $depths = [];
        foreach ($treeList as $key => $value) {
            $d = 0;
            while ($d < strlen($value) && $value[$d] == '_')
                $d++;
            array_push($depths, $d);

            array_push($tree, [
                'id' => $key,
                'name' => substr($value, $d),
                'children' => []
            ]);
        }
        $maxDepth = max($depths);

        for ($d=$maxDepth; $d > 0; $d--) {
            $i = 0;
            while ($i < sizeof($tree)) {
                if ($i < sizeof($depths) && $depths[$i] == $d) {
                    array_push($tree[$i-1]['children'], $tree[$i]);
                    unset($tree[$i]);
                    unset($depths[$i]);
                    $tree = array_values($tree);
                    $depths = array_values($depths);
                } else
                    $i++;
            }
        }

        $this->set('tags', $tags);
        $this->set('tree', $tree);
    }

    public function createCategory() {
        $name = $this->request->data('name');
        $description = $this->request->data('description');
        $parentName = $this->request->data('parentName');

        $res = $this->CategoriesTree->create($name, $description, $parentName);

        return $this->redirect([
            'controller' => 'categories_tree',
            'action' => 'manage',
            '?' => ['createCategory' => $res],
        ]);
    }

    public function removeCategory($categoryId) {
        $res = $this->CategoriesTree->remove($categoryId);

        return $this->redirect([
            'controller' => 'categories_tree',
            'action' => 'manage',
            '?' => ['removeCategory' => $res],
        ]);
    }

    public function attachTagToCategory() {
        $tagName = $this->request->data('tagName');
        $categoryName = $this->request->data('categoryName');

        $this->loadModel('Tags');
        $res = $this->Tags->attachToCategory($tagName, $categoryName);

        return $this->redirect([
            'controller' => 'categories_tree',
            'action' => 'manage',
            '?' => ['attachTagToCategory' => $res],
        ]);
    }

    public function detachTagFromCategory($tagId) {
        $this->loadModel('Tags');
        $res = $this->Tags->detachFromCategory($tagId);

        return $this->redirect([
            'controller' => 'categories_tree',
            'action' => 'manage',
            '?' => ['detachTagFromCategory' => $res],
        ]);
    }

    public function autocomplete($search) {
        $results = $this->CategoriesTree->Autocomplete($search);

        $this->loadComponent('RequestHandler');
        $this->set('results', $results);
        $this->set('_serialize', ['results']);
        $this->RequestHandler->renderAs($this, 'json');
    }
}