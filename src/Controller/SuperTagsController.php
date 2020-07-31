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


class SuperTagsController extends AppController
{
    /**
     * Controller name
     *
     * @var string
     * @access public
     */
    public $name = 'SuperTags';
    public $components = ['CommonSentence', 'Flash'];

    /**
     * Add a super tag
     *
     * @return void
     */
    public function createSuperTag(){
        $name = $this->request->data('name');
        $description = $this->request->data('description');
        $userId = CurrentUser::get('id');

        $added = $this->SuperTags->create($name, $description, $userId);

        return $this->redirect([
            'controller' => 'tags_super_tags',
            'action' => 'manage',
            '?' => ['superTagAdded' => $added],
        ]);
    }

    public function removeSuperTag($superTagId){
        $this->SuperTags->remove($superTagId);

        return $this->redirect([
            'controller' => 'tags_super_tags',
            'action' => 'manage',
        ]);
    }

    public function autocomplete($search) {
        $results = $this->SuperTags->Autocomplete($search);

        $this->loadComponent('RequestHandler');
        $this->set('results', $results);
        $this->set('_serialize', ['results']);
        $this->RequestHandler->renderAs($this, 'json');
    }
}
