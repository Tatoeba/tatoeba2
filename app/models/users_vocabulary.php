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
 * Model Class for users vocabulary.
 *
 * @package  Models
 * @author   HO Ngoc Phuong Trang <tranglich@gmail.com>
 * @license  Affero General Public License
 * @link     http://tatoeba.org
 */

class UsersVocabulary extends AppModel
{
    public $useTable = 'users_vocabulary';
    public $actsAs = array('Containable', 'Hashable');
    public $belongsTo = array(
        'Vocabulary' => array('foreignKey' => 'vocabulary_id'),
        'User' => array('foreignKey' => 'user_id')
    );

    /**
     * Add a vocabulary item to users_vocabulary pivot table.
     *
     * @param string $vocabularyId Binary version of vocabulary_id.
     * @param int    $userId       Id of current user.
     *
     * @return array               UsersVocabulary item.
     */
    public function add($vocabularyId, $userId)
    {
        if ($item = $this->findFirst($vocabularyId, $userId)) {
            return $item;
        }

        $data = array(
            'vocabulary_id' => $vocabularyId,
            'user_id' => $userId
        );
        
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

        $result = array(
            'conditions' => $conditions,
            'fields' => array('created'),
            'contain' => array(
                'Vocabulary' => array('id', 'lang', 'text', 'numSentences')
            ),
            'limit' => 50,
            'order' => 'created DESC'
        );

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
        return $this->find(
            'first',
            array(
                'conditions' => array(
                    'vocabulary_id' => $vocabularyId,
                    'user_id' => $userId
                )
            )
        );
    }
}
?>
