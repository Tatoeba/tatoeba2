<?php
/**
 * Tatoeba Project, free collaborative creation of languages corpuses project
 * Copyright (C) 2009 Allan SIMON <allan.simon@supinfo.com>
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
 * @author   Allan Simon <allan.simon@supinfo.com>
 * @license  Affero General Public License
 * @link     http://tatoeba.org
 */
namespace App\Model;


/**
 * Model for favorite
 *
 * @category Favorite
 * @package  Models
 * @author   Allan Simon <allan.simon@supinfo.com>
 * @license  Affero General Public License
 * @link     http://tatoeba.org
 */

class Favorite extends AppModel
{
    public $name = 'Favorite';
    public $useTable = 'favorites_users';

    public $actsAs = array(
        'Containable'
    );

    public $belongsTo = array(
        'Sentence' => array('foreignKey' => 'favorite_id')
    );

    /**
     * Get number of favorite sentences of a user.
     *
     * @param int $userId The user's id.
     *
     * @return array
     */
    public function numberOfFavoritesOfUser($userId)
    {
        $result = $this->find(
            'count',
            array(
                'conditions' => array(
                    'user_id' => $userId
                )
            )
        );

        return $result;
    }

    /**
     * Retrieve all favorites of a user.
     *
     * @param int $userId The user's id.
     *
     * @return array
     */

    public function getPaginatedFavoritesOfUser($userId)
    {

        $favorites = array(
            'fields' => array(
                'favorite_id'
            ),
            'conditions' => array(
                'Favorite.user_id' => $userId
            ),
            'contain' => array(
                'Sentence' => array(
                    'text',
                    'lang',
                    'correctness',
                    'Transcription' => array(
                        'User' => array('fields' => 'username'),
                    ),
                )
            )
        );

        return $favorites;
    }

    /**
     * Add a sentence to favorites.
     *
     * @param int $sentenceId Id of the sentence to favorite.
     * @param int $userId     Id of the user who want to add.
     *
     * @return bool
     */
    public function addFavorite($sentenceId, $userId)
    {
        $data = array(
            'favorite_id' => $sentenceId,
            'user_id' => $userId
        );

        $isSaved = $this->save($data);

        return $isSaved;
    }

    /**
     * Remove a sentence from favorites.
     *
     * @param int $sentenceId Id of the sentence to unfavorite.
     * @param int $userId     Id of the user who want to remove.
     *
     * @return bool
     */
    public function removeFavorite($sentenceId, $userId)
    {
        $conditions = array(
            'Favorite.favorite_id' => $sentenceId,
            'Favorite.user_id' => $userId
        );

        $isDeleted = $this->deleteAll($conditions, false);

        return $isDeleted;
    }


    /**
     * Indicates whether a sentence has been favorited by a user or not.
     *
     * @param int $sentenceId Id of the sentence.
     * @param int $userId     Id of the user.
     *
     * @return bool
     */
    public function isSentenceFavoritedByUser($sentenceId, $userId)
    {
        $result = $this->find(
            'first',
            array(
                'conditions' => array(
                    'favorite_id' => $sentenceId,
                    'user_id' => $userId
                )
            )
        );
        return !empty($result);
    }
}
