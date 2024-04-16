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
 * @link     https://tatoeba.org
 */
namespace App\Controller;

use App\Controller\AppController;
use Cake\Event\Event;

/**
 * Controller for links between sentences. Links specify which sentences are
 * translations of which other sentences.
 *
 * @category Links
 * @package  Controllers
 * @author   HO Ngoc Phuong Trang <tranglich@gmail.com>
 * @license  Affero General Public License
 * @link     https://tatoeba.org
 */
class LinksController extends AppController
{
    public function initialize() {
        parent::initialize();
        $request = $this->request;
        $noCsrfActions = [
            'add',
            'delete'
        ];
        if (in_array($request->getParam('action'), $noCsrfActions)) {
            $this->components()->unload('Csrf');
        }
    }

    /**
     * Before filter.
     *
     * @return void
     */
    public function beforeFilter(Event $event)
    {
        if($this->request->is('ajax')) {
            $this->Security->config('unlockedActions', [
                'add',
                'delete'
            ]);
        }
        
        return parent::beforeFilter($event);
    }

    private function _renderTranslationsOf($sentenceId, $message)
    {
        $this->loadModel('Sentences');
        $langFilter = $this->request->data['langFilter'] ?? 'und';
        $translations = $this->Sentences->getSentenceWith($sentenceId, ['translations' => true], $langFilter)->translations;

        $this->set('sentenceId', $sentenceId);
        $this->set('translations', $translations);
        $this->set('message', $message);
        $this->set('langFilter', $langFilter);
        $this->render('/Sentences/translations_group');
    }

    private function _returnSentenceAndTranslations($sentenceId) {
        $this->loadComponent('RequestHandler');
        $this->loadModel('Sentences');
        $translationLang = $this->request->getQuery('translationLang');
        $sentence = $this->Sentences->getSentenceWith($sentenceId, ['translations' => true], $translationLang);
        $this->set('sentence', $sentence);
        $this->set('_serialize', ['sentence']);
        $this->RequestHandler->renderAs($this, 'sentences_json');
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
        $saved = $this->Links->add($sentenceId, $translationId);

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

        $acceptsJson = $this->request->accepts('application/json');
        if ($acceptsJson) {
            $this->_returnSentenceAndTranslations($sentenceId);
        } else if ($this->request->is('ajax')) {
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
        $saved = $this->Links->deletePair($sentenceId, $translationId);

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

        $acceptsJson = $this->request->accepts('application/json');
        if ($acceptsJson) {
            $this->_returnSentenceAndTranslations($sentenceId);
        } else if ($this->request->is('ajax')) {
            if (isset($this->request->data['returnTranslations'])
                && (bool)$this->request->data['returnTranslations'])
                $this->_renderTranslationsOf($sentenceId, $flashMessage);
        } else {
            $this->flash($flashMessage, '/sentences/show/'.$sentenceId);
        }
    }
}
