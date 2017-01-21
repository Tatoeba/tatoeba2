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
 * @link     http://tatoeba.org
 */

/**
 * Controller for users languages.
 *
 * @category SentencesLists
 * @package  Controllers
 * @author   HO Ngoc Phuong Trang <tranglich@gmail.com>
 * @license  Affero General Public License
 * @link     http://tatoeba.org
 */
class UsersLanguagesController extends AppController
{
    public $uses = array('User', 'UsersLanguages');

    public function save()
    {
        $userId = $this->request->data['UsersLanguages']['of_user_id'];
        $username = $this->User->getUserNameFromId($userId);

        if (empty($this->request->data['UsersLanguages']['id'])) {
            $canSave = true;
        } else {
            $id = $this->request->data['UsersLanguages']['id'];
            $langInfo = $this->UsersLanguages->getLanguageInfo($id);
            $canSave = $langInfo['by_user_id'] == CurrentUser::get('id');
        }

        if ($canSave) {
            $allowedFields = array('id', 'language_code', 'level', 'details');
            $language = $this->filterKeys($this->request->data['UsersLanguages'], $allowedFields);
            $language['of_user_id'] = CurrentUser::get('id');
            $language['by_user_id'] = CurrentUser::get('id');
            if ($language['level'] < 0) {
                $language['level'] = null;
            }
            $this->UsersLanguages->save($language);
        } else {
            $this->Flash->set(__('You cannot edit this language.'));
        }

        $this->redirect(
            array(
                'controller' => 'user',
                'action' => 'profile',
                $username
            )
        );
    }


    public function delete($id)
    {
        $userId = CurrentUser::get('id');
        $langInfo = $this->UsersLanguages->getLanguageInfo($id);
        $canDelete = $langInfo['by_user_id'] == $userId;

        if ($canDelete) {
            $this->UsersLanguages->delete($id, false);
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
