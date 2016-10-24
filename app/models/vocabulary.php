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

class Vocabulary extends AppModel
{
    public $useTable = 'vocabulary';
    public $belongsTo = array('UsersVocabulary', 'Sentence');
    public $actsAs = array('Hashable');

    /**
     * Adds an item into the vocabulary list of current user.
     *
     * @param $lang string Language of the vocabulary item.
     * @param $text string Text of the vocabulary item.
     *
     * @return $data array
     */
    public function addItem($lang, $text)
    {
        $text = trim($text);
        if (empty($text) || empty($lang)) {
            return null;
        }
        
        $hash = $this->makeHash($lang, $text);

        $data = array(
            'hash' => $hash,
            'lang' => $lang,
            'text' => $text
        );

        if ($vocabulary = $this->findByBinary($hash, 'hash')) {
            $numSentences = $this->_updateNumSentences($vocabulary['Vocabulary']);

            $data['numSentences'] = $numSentences;
        } else {
            $numSentences = $this->_getNumberOfSentences($lang, $text);

            $data['numSentences'] = $numSentences;

            $this->save($data);
        }

        $this->UsersVocabulary->add($this->id, CurrentUser::get('id'));

        return $data;
    }

    /**
     * Returns the number of sentences for $text in language $lang.
     *
     * This uses the search engine (Sphinx) to count the number of search result
     * for an exact search on $text in language $lang.
     *
     * @param $lang string Language of the vocabulary item
     * @param $text string Text of the vocabulary item.
     *
     * @return int
     */
    private function _getNumberOfSentences($lang, $text)
    {
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
     * Returns array to use in $this->paginate, to retrieve all the vocabulary
     * items in language $lang for which sentences are needed.
     * We assume that a vocabulary item needs sentences if there are less than
     * 10 sentences for it.
     * The vocabulary items are sorted be number of sentences.
     *
     * @param $lang string
     *
     * @return array
     */
    public function getPaginatedVocabulary($lang = null)
    {
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
     * Increment vocabulary numSentences value by one if sentence contains
     * vocabulary.
     *
     * @param  int    $id       Vocabulary item id.
     * @param  string $sentence Sentence which should contain vocabulary text.
     *
     * @return int              Vocaubualry numSentences value.
     */
    public function incrementNumSentences($id, $sentence)
    {
        $vocabulary = $this->findById($id);

        $vocabularyText = $vocabulary['Vocabulary']['text'];
        $numSentences = intval($vocabulary['Vocabulary']['numSentences']);

        if ($this->_sentenceContainsText($sentence, $vocabularyText)) {
            $numSentences ++;

            $data = array(
                'id' => $id,
                'numSentences' => $numSentences
            );

            if ($numSentences) {
                $this->save($data);
            }
        }

        return $numSentences;
    }

    /**
     * Return true if sentence contains given text.
     *
     * @param  string $sentence Haystack to be searched.
     * @param  string $text     Needle to be searched for.
     *
     * @return boolean
     */
    private function _sentenceContainsText($sentence, $text)
    {
        return mb_stripos($sentence, $text) !== false;
    }

    /**
     * Sync the numSentences column on vocabulary items with the Sphinx index.
     *
     * @param  array $vocabulary Single Vocabulary item or array of items.
     *
     * @return array Array of vocabulary items.
     */
    public function syncNumSentences($vocabulary)
    {
        if (!empty($vocabulary) && !isset($vocabulary[0])) {
            $vocabulary = array($vocabulary);
        }

        return array_map(function ($item) {
            $numSentences = $this->_updateNumSentences($item['Vocabulary']);

            $item['Vocabulary']['numSentences'] = $numSentences;

            return $item;
        }, $vocabulary);
    }

    /**
     * Updates the number of sentences for a vocabulary item.
     *
     * @param array $vocabulary Vocabulary item.
     *
     * @return $int Number of sentences in Sphinx index.
     */
    private function _updateNumSentences($vocabulary)
    {
        $numSentences = $vocabulary['numSentences'];

        $indexedNumSentences = $this->_getNumberOfSentences(
            $vocabulary['lang'],
            $vocabulary['text']
        );

        if ($numSentences != $indexedNumSentences) {
            $data = array(
                'id' => $vocabulary['id'],
                'numSentences' => $indexedNumSentences
            );

            $this->save($data);
        }

        return $indexedNumSentences;
    }
}
?>
