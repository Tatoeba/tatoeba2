<?php
/**
 * Tatoeba Project, free collaborative creation of multilingual corpuses project
 * Copyright (C) 2016  HO Ngoc Phuong Trang <tranglich@gmail.com>
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
 * Model class for vocabulary.
 *
 * @package  Models
 * @author   HO Ngoc Phuong Trang <tranglich@gmail.com>
 * @license  Affero General Public License
 * @link     http://tatoeba.org
 */
App::import('Vendor', 'murmurhash3');

class Vocabulary extends AppModel
{
    public $useTable = 'vocabulary';
    public $belongsTo = array('UsersVocabulary', 'Sentence');


    /**
     * 
     */
    public function addItem($lang, $text) {
        $text = trim($text);
        if (empty($text) || empty($lang)) {
            return null;
        }
        $numSentences = $this->_getNumberOfSentences($lang, $text);
        $hash = murmurhash3($lang.$text);
        $data = array(
            'id' => $hash,
            'lang' => $lang,
            'text' => $text,
            'numSentences' => $numSentences
        );

        $this->save($data);
        $this->UsersVocabulary->add($hash, CurrentUser::get('id'));

        return $data;
    }

    private function _getNumberOfSentences($lang, $text) {
        $this->Behaviors->attach('Sphinx');
        $index = array($lang . '_main_index', $lang . '_delta_index');
        $sphinx = array(
            'index' => $index,
            'matchMode' => SPH_MATCH_EXTENDED2
        );
        $query = '="'.$text.'"';
        return $this->Sentence->find('count', array(
            'sphinx' => $sphinx,
            'search' => $query
        ));
    }


    /**
     *
     */
    public function getPaginatedVocabulary($lang = null) {
        $conditions = array(
            'numSentences <' => 10,
            'numAdded >' => 0
        );
        if (!empty($lang)) {
            $conditions['lang'] = $lang;
        }

        $result = array(
            'conditions' => $conditions,
            'fields' => array('id', 'lang', 'text', 'numSentences'),
            'limit' => 50,
            'order' => 'numSentences ASC'
        );

        return $result;
    }


    /**
     *
     */
    public function updateNumSentences($id) {
        $vocabularyId = hex2bin($id);
        $vocabulary = $this->findById($vocabularyId);

        $numSentences = $this->_getNumberOfSentences(
            $vocabulary['Vocabulary']['lang'],
            $vocabulary['Vocabulary']['text']
        );

        $data = array(
            'id' => $vocabularyId,
            'numSentences' => $numSentences
        );

        if ($numSentences) {
            $this->save($data);
        }
    }
}
?>
