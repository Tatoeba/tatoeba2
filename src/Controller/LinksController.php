<?php
/**
 * Tatoeba Project, free collaborative creation of multilingual corpuses project
 * Copyright (C) 2009-2011  HO Ngoc Phuong Trang <tranglich@gmail.com>
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
namespace App\Controller;

use App\Controller\AppController;


/**
 * Controller for links between sentences. Links specify which sentences are
 * translations of which other sentences.
 *
 * @category Links
 * @package  Controllers
 * @author   HO Ngoc Phuong Trang <tranglich@gmail.com>
 * @license  Affero General Public License
 * @link     http://tatoeba.org
 */
class LinksController extends AppController
{
    /**
     * Before filter.
     *
     * @return void
     */
    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);
        // setting actions that are available to everyone, even guests
        if($this->request->is('ajax')) {
          $this->Security->unlockedActions = array('add', 'delete');
        }
    }

    private function _renderTranslationsOf($sentenceId, $message)
    {
        $this->loadModel('Sentence');
        $langFilter = isset($this->request->data['langFilter']) ? $this->request->data['langFilter'] : 'und';
        $translations = $this->Sentence->getTranslationsOf($sentenceId, $langFilter);

        $this->set('sentenceId', $sentenceId);
        $this->set('translations', $translations);
        $this->set('message', $message);
        $this->set('langFilter', $langFilter);
        $this->render('/Sentences/translations_group');
    }

    /**
     * Link sentences.
     *
     * @param int $sentenceId    Id of the sentence.
     * @param int $translationId Id of the translation to link to.
     *
     * @return void
     */
    public function add($sentenceId, $translationId)
    {
        $sentenceId = Sanitize::paranoid($sentenceId);
        $translationId = Sanitize::paranoid($translationId);

        $saved = $this->Link->add($sentenceId, $translationId);

        if ($saved) {
            $flashMessage = format(
                __(
                    'Sentences #{firstNumber} and #{secondNumber} are now '.
                    'direct translations of each other.',
                    true
                ),
                array('firstNumber' => $sentenceId, 'secondNumber' => $translationId)
            );
        } else {
            $flashMessage = __(
                'An error occurred while saving. '.
                'Please try again or contact us to report this.',
                true
            );
        }

        $this->set('saved', $saved);

        if ($this->request->is('ajax')) {
            if (isset($this->request->data['returnTranslations'])
                && (bool)$this->request->data['returnTranslations'])
                $this->_renderTranslationsOf($sentenceId, $flashMessage);
        } else {
            $this->flash($flashMessage, '/sentences/show/'.$sentenceId);
        }
    }


    /**
     * Unlink sentences.
     *
     * @param int $sentenceId    Id of the sentence.
     * @param int $translationId Id of the translation to unlink.
     *
     * @return void
     */
    public function delete($sentenceId, $translationId)
    {
        $sentenceId = Sanitize::paranoid($sentenceId);
        $translationId = Sanitize::paranoid($translationId);

        $saved = $this->Link->deletePair($sentenceId, $translationId);

        if ($saved) {
            $flashMessage = format(
                __(
                    'Sentences #{firstNumber} and #{secondNumber} are no longer '.
                    'direct translations of each other.',
                    true
                ),
                array('firstNumber' => $sentenceId, 'secondNumber' => $translationId)
            );
        } else {
            $flashMessage = __(
                'An error occurred while unlinking. '.
                'Please try again or contact us to report this.',
                true
            );
        }

        $this->set('saved', $saved);

        if ($this->request->is('ajax')) {
            if (isset($this->request->data['returnTranslations'])
                && (bool)$this->request->data['returnTranslations'])
                $this->_renderTranslationsOf($sentenceId, $flashMessage);
        } else {
            $this->flash($flashMessage, '/sentences/show/'.$sentenceId);
        }
    }
}
