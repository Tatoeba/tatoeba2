<?php
/**
 * Tatoeba Project, free collaborative creation of multilingual corpuses project
 * Copyright (C) 2009-2010  HO Ngoc Phuong Trang <tranglich@gmail.com>
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
 *
 * PHP version 5
 *
 * @category PHP
 * @package  Tatoeba
 * @author   HO Ngoc Phuong Trang <tranglich@gmail.com>
 * @license  Affero General Public License
 * @link     https://tatoeba.org
 */

namespace App\Controller;

use App\Controller\AppController;
use App\Model\CurrentUser;
use Cake\Event\Event;

/**
 * Controller for users languages.
 *
 * @category SentencesLists
 * @package  Controllers
 * @author   HO Ngoc Phuong Trang <tranglich@gmail.com>
 * @license  Affero General Public License
 * @link     https://tatoeba.org
 */
class UsersLanguagesController extends AppController
{
    public $uses = array('UsersLanguages');
    public $components = array('Flash');

    public function beforeFilter(Event $event)
    {
        $this->Security->config('unlockedActions', [
            'save',
        ]);

        return parent::beforeFilter($event);
    }

    private function respondError($message)
    {
        $this->setResponse($this->response->withStatus(400, 'Validation error'));
        $this->set(compact('message'));
        $this->set('_serialize', ['message']);
    }

    public function save()
    {
        $isAngular = $this->request->accepts('application/json');
        if ($isAngular) {
            $this->loadComponent('RequestHandler');
            $this->RequestHandler->renderAs($this, 'json');
        }

        try {
            $savedLanguage = $this->UsersLanguages->saveUserLanguage(
                $this->request->getData(),
                CurrentUser::get('id')
            );
        } catch (\PDOException $e) {
            $message = __('This language has already been added to your profile.');
            if ($isAngular) {
                return $this->respondError($message);
            } else {
                $this->Flash->set($message);
                return $this->redirect(
                    array(
                        'controller' => 'user',
                        'action' => 'language'
                    )
                 );
            }
        }

        if (!$savedLanguage) {
            $message = __('You cannot save this language.');
            if ($isAngular) {
                return $this->respondError($message);
            } else {
                $this->Flash->set($message);
                $this->redirect(
                    array(
                        'controller' => 'user',
                        'action' => 'language'
                    )
                );
            }
        } else {
            if ($isAngular) {
                $languages = $this->UsersLanguages->getLanguagesByUser(CurrentUser::get('id'));
                $this->set(compact('languages'));
                $this->set('_serialize', ['languages']);
                $this->RequestHandler->renderAs($this, 'json');
            } else {
                $this->redirect(
                    array(
                        'controller' => 'user',
                        'action' => 'profile',
                        CurrentUser::get('username')
                    )
                );
            }
        }
    }


    public function delete($id)
    {
        $userId = CurrentUser::get('id');
        $isDeleted = $this->UsersLanguages->deleteUserLanguage($id, $userId);

        if ($isDeleted) {
            $this->Flash->set(__('Language deleted'));
        } else {
            $this->Flash->set(__('You cannot delete this language.'));
        }

        $this->redirect(
            array(
                'controller' => 'user',
                'action' => 'profile',
                CurrentUser::get('username')
            )
        );
    }
}
