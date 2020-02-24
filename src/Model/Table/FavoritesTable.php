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
 */
namespace App\Model\Table;

use Cake\ORM\Table;


class FavoritesTable extends Table
{
    public $name = 'Favorite';

    public function initialize(array $config)
    {
        $this->setTable('favorites_users');
        
        $this->belongsTo('Sentences', ['foreignKey' => 'favorite_id']);
    }

    /**
     * Get number of favorite sentences of a user.
     *
     * @param int $userId The user's id.
     *
     * @return array
     */
    public function numberOfFavoritesOfUser($userId)
    {
        return $this->find()
            ->where(['user_id' => $userId])
            ->count();
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
            'fields' => [
                'favorite_id'
            ],
            'conditions' => [
                'Favorites.user_id' => $userId
            ],
            'contain' => [
                'Sentences' => [
                    'fields' => ['id', 'text', 'lang', 'correctness'],
                    'Transcriptions' => array(
                        'Users' => ['fields' => ['username']],
                    ),
                ]
            ]
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
        $data = $this->newEntity([
            'favorite_id' => $sentenceId,
            'user_id' => $userId
        ]);

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
        $isDeleted = $this->deleteAll([
            'favorite_id' => $sentenceId,
            'user_id' => $userId
        ]);

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
        $result = $this->find()
            ->where([
                'favorite_id' => $sentenceId,
                'user_id' => $userId
            ])
            ->first();
        return !empty($result);
    }
}
