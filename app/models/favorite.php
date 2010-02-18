<?php
/**
 * Tatoeba Project, free collaborativ creation of languages corpuses project
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
    public $useTable = 'sentences';

    public $actsAs = array('ExtendAssociations','Containable');

    public $hasAndBelongsToMany = array(
        'User' => array(
            'className' => 'User',
            'joinTable' => 'favorites_users',
            'foreignKey' => 'user_id',
            'associationForeignKey' => 'favorite_id',
        )
    );

    /**
     * get number of favorite sentence of a user
     *
     * @param int $userId the user's id
     *
     * @return array
     */
    public function numberOfFavoritesOfUser($userId)
    {
        $result = $this->query(
            "
            SELECT count(user_id) AS numberOfFavorites FROM favorites_users
            WHERE user_id = $userId
            "
        );

        return $result[0][0]["numberOfFavorites"];
    }

    /**
     * retrieve all favorites of a user
     *
     * @param int $userId user to retrieve favorites 
     * 
     * @return array 
     */

    public function getAllFavoritesOfUser($userId)
    {
        $favorites = $this->User->find(
            'first',
            array(
                'conditions' => array('User.id' => $userId),
                'fields' => array('id', 'username'),
                'contain' => array('Favorite')
            )
        );
        
        return $favorites;
    }

    /**
     * add a sentence to current user's ones
     *
     * @param int $sentenceId id of the sentence to favorite
     * @param int $userId     id of the user who want to add
     *
     * @return bool
     */

    public function addFavorite($sentenceId, $userId)
    {
        // habtmAdd() was behaving strangely so we're doing it manually
        $this->query("
            INSERT INTO `favorites_users` (`favorite_id`,`user_id`) 
            VALUES ($sentenceId, $userId)
        ");
        
        // TODO Find a way not to return always true.
        // $this->query() won't return anything if it's an "INSERT"
        return true;
    } 

    /**
     * remove a sentence to current user's ones
     *
     * @param int $sentenceId id of the sentence to unfavorite
     * @param int $userId     id of the user who want to remove
     *
     * @return bool
     */

    public function removeFavorite($sentenceId, $userId)
    {
        // habtmDelete() was behaving strangely so we're doing it manually
        $this->query("
            DELETE FROM `favorites_users` 
            WHERE favorite_id = $sentenceId AND user_id = $userId
        ");
        
        // TODO Find a way not to return always true.
        // $this->query() won't return anything if it's a "DELETE"
        return true;
    } 
}
?>
