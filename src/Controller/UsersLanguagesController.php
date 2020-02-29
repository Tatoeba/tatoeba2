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

    public function save()
    {
        try {
            $savedLanguage = $this->UsersLanguages->saveUserLanguage(
                $this->request->getData(),
                CurrentUser::get('id')
            );
        } catch (\PDOException $e) {
            $this->Flash->set(__('This language has already been added to your profile.'));
            return $this->redirect(
                array(
                    'controller' => 'user',
                    'action' => 'language'
                )
             );
        }

        if (empty($savedLanguage)) {
            $lang = $this->request->getData('lang');
            if (empty($lang) || $lang == 'und') {
                $this->Flash->set(__('No language selected.'));
            } else {
                $this->Flash->set(__('You cannot save this language.'));
            }
            $this->redirect(
                array(
                    'controller' => 'user',
                    'action' => 'language'
                )
            );
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
