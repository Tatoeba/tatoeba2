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


class UsersVocabularyTable extends Table
{
    public function initialize(Array $config)
    {
        $this->belongsTo('Vocabulary');
        $this->belongsTo('Users');

        $this->addBehavior('Timestamp');
    }

    /**
     * Add a vocabulary item to users_vocabulary pivot table.
     *
     * @param string $vocabularyId Binary version of vocabulary_id.
     * @param int    $userId       Id of current user.
     *
     * @return Cake\ORM\Entity|false
     */
    public function add($vocabularyId, $userId)
    {
        $data = $this->newEntity([
            'vocabulary_id' => $vocabularyId,
            'user_id' => $userId
        ]);

        return $this->save($data);
    }

    /**
     * Get paginated vocabulary for user.
     *
     * @param  int    $userId ID for user.
     * @param  string $lang   Language.
     *
     * @return array
     */
    public function getPaginatedVocabularyOf($userId, $lang = null)
    {
        $conditions = array('UsersVocabulary.user_id' => $userId);
        if (!empty($lang)) {
            $conditions['lang'] = $lang;
        }

        $result = [
            'conditions' => $conditions,
            'fields' => ['created', 'user_id'],
            'contain' => [
                'Vocabulary' => [
                    'fields' => ['id', 'lang', 'text', 'numSentences', 'numAdded']
                ]
            ],
            'limit' => 50,
            'order' => ['created' => 'DESC']
        ];

        return $result;
    }

    /**
     * Find and return the first item with given vocabularyId and userId.
     *
     * @param  int $vocabularyId Vocabulary item id.
     * @param  int $userId       User id.
     *
     * @return array|boolean
     */
    public function findFirst($vocabularyId, $userId)
    {
        return $this->find()
            ->where([
                'vocabulary_id' => $vocabularyId,
                'user_id' => $userId
            ])
            ->first();
    }
}
