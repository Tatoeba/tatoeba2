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
    public $actsAs = array('Containable');
    public $belongsTo = array(
        'Vocabulary' => array('foreignKey' => 'vocabulary_id'),
        'User' => array('foreignKey' => 'user_id')
    );


    /**
     *
     */
    public function add($vocabularyId, $userId) {
        if ($item = $this->findByBinary($vocabularyId)) {
            return $item;
        }

        $data = array(
            'vocabulary_id' => $vocabularyId,
            'user_id' => $userId
        );
        
        return $this->save($data);
    }

    /**
     *
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
     * Find a users_vocabulary record by a binary vocabulary_id value.
     *
     * @param  string $binary Binary vocabulary_id value.
     *
     * @return array
     */
    public function findByBinary($binary)
    {
        $binary = $this->_getPaddedBinary($binary);

        return $this->find(['vocabulary_id' => $binary]);
    }

    /**
     * Convert a binary id to a padded binary id.
     *
     * @param  string $binary Binary id value.
     *
     * @return string
     */
    private function _getPaddedBinary($binary)
    {
        $hex = bin2hex($binary);

        $hex = str_pad($hex, 32, 0);

        return hex2bin($hex);
    }
}
?>
