<?php
/**
 * Tatoeba Project, free collaborative creation of languages corpuses project
 * Copyright (C) 2010  HO Ngoc Phuong Trang <tranglich@gmail.com>
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

use App\Model\CurrentUser;
use Cake\ORM\Table;
use Cake\Core\Configure;
use Cake\Utility\Hash;

class SentencesSentencesListsTable extends Table
{
    public $name = 'SentencesSentencesLists';
    public $useTable = 'sentences_sentences_lists';
    public $actsAs = array('Containable');

    public $belongsTo = array(
        'Sentence' => array('foreignKey' => 'sentence_id'),
        'SentencesList' => array('foreignKey' => 'sentences_list_id')
    );

    public function initialize(array $config)
    {
        $this->belongsTo('Sentences');
        
        $this->addBehavior('Timestamp');
        if (Configure::read('Search.enabled')) {
            $this->addBehavior('Sphinx', ['alias' => $this->getAlias()]);
        }
    }

    /**
     * Returns value of $this->paginate, for paginating sentences of a list.
     *
     * @param int    $listId Id of the list.
     * @param int    $limit  Number of sentences per page.
     * @param string $translationsLang  Limit translations in that language
     *
     * @return array
     */
    public function getPaginatedSentencesInList($listId, $limit, $translationsLang)
    {
        $contain = ['Sentences' => $this->Sentences->paginateContain()];
        
        return [
            'limit' => $limit,
            'conditions' => ['sentences_list_id' => $listId],
            'contain' => $contain,
            'fields' => ['created', 'sentence_id'],
            'order' => ['created' => 'DESC']
        ];
    }


    public function getListsForSentence($sentenceId)
    {
        return $this->find()
            ->where([
                'sentence_id' => $sentenceId,
                'user_id' => CurrentUser::get('id')
            ])
            ->select(['created'])
            ->contain([
                'SentencesLists' => [
                    'fields' => ['id', 'name', 'visibility']
                ]
            ])
            ->order(['visibility', 'SentencesSentencesLists.created' => 'DESC'])
            ->all();
    }

    public function sphinxAttributesChanged(&$attributes, &$values, &$isMVA, $entity) {
        $sentenceId = $entity->sentence_id;
        $isMVA = true;
        $attributes[] = 'lists_id';
        $records = $this->find('all')
            ->where(['sentence_id' => $sentenceId])
            ->select('sentences_list_id')
            ->toList();
        $listsId = Hash::extract($records, '{n}.sentences_list_id');
        $values = array($sentenceId => array($listsId));
    }
}
