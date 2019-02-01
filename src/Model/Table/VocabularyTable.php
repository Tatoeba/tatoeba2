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
 */
namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Core\Configure;
use Cake\Database\Schema\TableSchema;
use App\Model\CurrentUser;
use App\Utility\Search;


class VocabularyTable extends Table
{
    protected function _initializeSchema(TableSchema $schema)
    {
        $schema->setColumnType('text', 'text');
        $schema->setColumnType('created', 'string');
        $schema->setColumnType('hash', 'string');
        return $schema;
    }

    public function initialize(Array $config)
    {
        $this->setTable('vocabulary');

        $this->belongsTo('UsersVocabulary');
        $this->belongsTo('Sentences');

        $this->addBehavior('Timestamp');
        $this->addBehavior('Hashable');
        if (Configure::read('Search.enabled')) {
            $this->addBehavior('Sphinx', ['alias' => $this->getAlias()]);
        }
    }

    public function beforeSave($event, $entity, $options)
    {
        if ($entity->isNew() || $entity->dirty('lang') || $entity->dirty('text')) {
            $lang = $entity->lang;
            $text = $entity->text;
            $entity->numSentences = $this->_getNumberOfSentences($lang, $text);
        }
    }

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
        $hash = $this->padHashBinary($hash);
        $result = $this->_hasDuplicate($hash, $lang, $text);

        if (!$result) {
            $data = $this->newEntity([
                'hash' => $hash,
                'lang' => $lang,
                'text' => $text,
            ]);
            $result = $this->save($data);
        }

        $this->UsersVocabulary->add($result->id, CurrentUser::get('id'));

        if (Configure::read('Search.enabled')) {
            $result->query = Search::exactSearchQuery($text);
        }

        return $result;
    }

    /**
     * Return true if land and text have true duplicate.
     *
     * @param  string  $hash Vocabulary hash value.
     * @param  string  $lang Vocabulary language.
     * @param  string  $text Vocabulary text.
     *
     * @return boolean|array
     */
    private function _hasDuplicate($hash, $lang, $text)
    {
        $vocabularyItems = $this->findAllByHash($hash)->toList();

        foreach ($vocabularyItems as $vocabularyItem) {
            if ($this->confirmDuplicate($text, $lang, $vocabularyItem)) {
                $numSentences = $this->_updateNumSentences($vocabularyItem);
                return $vocabularyItem;
            }
        }

        return null;
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
        if (!Configure::read('Search.enabled')) {
            return null;
        }
        $index = array($lang . '_main_index', $lang . '_delta_index');
        $sphinx = array(
            'index' => $index,
            'matchMode' => SPH_MATCH_EXTENDED2
        );
        $query = Search::exactSearchQuery($text);
        return $this->Sentences->find('withSphinx', [
            'sphinx' => $sphinx,
            'search' => $query
        ])->count();
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
        $conditions = [
            'numSentences <' => 10,
            'numAdded >' => 0
        ];
        if (!empty($lang)) {
            $conditions['lang'] = $lang;
        }

        $result = [
            'conditions' => $conditions,
            'fields' => ['id', 'lang', 'text', 'numSentences'],
            'limit' => 50,
            'order' => ['numSentences' => 'ASC']
        ];

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
        $vocabulary = $this->get($id);

        if ($this->_sentenceContainsText($sentence, $vocabulary->text)) {
            $vocabulary->numSentences++;
            $result = $this->save($vocabulary);
        } else {
            $result = $vocabulary;
        }

        return $result->numSentences;
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
     */
    public function syncNumSentences($results)
    {
        return $results->map(function ($item) {
            $vocabulary = $item->vocabulary;
            $numSentences = $this->_updateNumSentences($vocabulary);
            $item->vocabulary->numSentences = $numSentences;
            return $item;
        });
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

        if (is_null($indexedNumSentences)) {
            return $numSentences;
        } else {
            if ($numSentences !== $indexedNumSentences) {
                $data = $this->get($vocabulary['id']);
                $data->numSentences = $indexedNumSentences;
                $this->save($data);
            }
            return $indexedNumSentences;
        }
    }
}
