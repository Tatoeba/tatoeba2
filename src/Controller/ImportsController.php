<?php
/**
 * Tatoeba Project, free collaborative creation of multilingual corpuses project
 * Copyright (C) 2010  Allan SIMON <allan.simon@supinfo.com>
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
 * @author   Allan SIMON <allan.simon@supinfo.com>
 * @license  Affero General Public License
 * @link     https://tatoeba.org
 */
namespace App\Controller;

use App\Controller\AppController;
use Cake\Event\Event;

/**
 * Controller for sentence imports.
 *
 * @category Imports
 * @package  Controllers
 * @author   Allan SIMON <allan.simon@supinfo.com>
 * @license  Affero General Public License
 * @link     https://tatoeba.org
 */
class ImportsController extends AppController
{
    public $name = "Imports";
    public $uses = "Sentence";

    /**
     * Used to treat data send from import form
     * will import a list of single sentences from a file
     *
     * @return void
     */
    public function import_single_sentences()
    {
        $sentenceLang = $this->request->getData('sentences_lang');
        $userId = $this->request->getData('user_id');
        $sentencesListFile = $this->request->getData('file');
        if ( !$this->_common_upload_check($sentencesListFile)) {
            return $this->redirect(
                array(
                    'controller' => 'sentences',
                    'action' => 'import'
                )
            );
        };

        // TODO improve this, as this can lead to several hundreds of addition
        // maybe we can do this in only one or two insert, and in one update
        // of the langStat table instead of doing this for each sentences
        $lines = file($sentencesListFile['tmp_name'], FILE_IGNORE_NEW_LINES);
        foreach ($lines as $sentence) {
            $this->Sentence->saveNewSentence($sentence, $sentenceLang, $userId);
        }

        return $this->redirect(
            array(
                'controller' => 'sentences',
                'action' => 'import'
            )
        );

    }

    /**
     * Treat and import a list of sentences and translation (tab separated),
     * from a file
     *
     * @return void
     */
    public function import_sentences_with_translation()
    {
        $sentenceLang = $this->request->getData('sentences_lang');
        $translationLang = $this->request->getData('translations_lang');
        $userId = $this->request->getData('user_id');
        $sentencesListFile = $this->request->getData('file');

        if ( !$this->_common_upload_check($sentencesListFile)) {
            return $this->redirect(
                array(
                    'controller' => 'sentences',
                    'action' => 'import'
                )
            );
        };

        // TODO improve this
        $lines = file($sentencesListFile['tmp_name'], FILE_IGNORE_NEW_LINES);
        foreach ($lines as $line) {
            $sentences = explode("\t", $line, 2);
            $this->Sentence->saveNewSentenceWithTranslation(
                $sentences[0],
                $sentenceLang,
                $sentences[1],
                $translationLang,
                $userId
            );
        }

        return $this->redirect(
            array(
                'controller' => 'sentences',
                'action' => 'import'
            )
        );
    }

    /**
     * Do some basic check to see if there's no problem with the uploaded file
     *
     * @param array $file The file structure
     *
     * @TODO add more checks (MIME type etc.)
     * @TODO move this in something more general
     *
     * @return bool
     */
    private function _common_upload_check ($file)
    {

        if (isset($file['error']) && $file['error'] != UPLOAD_ERR_OK) {
            return false;
        }

        if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
            return false;
        }
        return true;
    }
}
