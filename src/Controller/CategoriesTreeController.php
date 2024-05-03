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
namespace App\Controller;

use App\Controller\AppController;
use Cake\Core\Configure;
use Cake\Event\Event;


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

    public function beforeFilter(Event $event)  {
        // Not ready for production yet
        if (!Configure::read('debug') && !Configure::read('Tatoeba.devStylesheet')) {
            return $this->response->withStatus(403);
        }
        return parent::beforeFilter($event);
    }

    public function manage(){
        $this->loadModel('Tags');
        $tags = [];
        $tagList = $this->Tags->find('all')
            ->select(['id', 'name', 'nbrOfSentences', 'category_id'])
            ->order(['name']);
        foreach ($tagList as $tag) {
            $category = ($tag['category_id'] == null) ? -1 : $tag['category_id'];
            if (!array_key_exists($category, $tags)) {
                $tags[$category] = [];
            }
            array_push($tags[$category], [
                'id' => $tag['id'],
                'name' => $tag['name'], 
                'nbrOfSentences' => $tag['nbrOfSentences']
            ]);
            
        }
        
        $tree = $this->CategoriesTree->find('threaded', [
            'order' => 'name'
        ]);

        $this->set('tags', $tags);
        $this->set('tree', $tree);
    }

    public function createorEditCategory() {
        $name = $this->request->getData('name');
        $description = $this->request->getData('description');
        $parentName = $this->request->getData('parentName');

        $res = $this->CategoriesTree->createOrEdit($name, $description, $parentName);

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
        $tagName = $this->request->getData('tagName');
        $categoryName = $this->request->getData('categoryName');

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