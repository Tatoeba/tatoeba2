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
 * Controller for contributions.
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
            'foreignKey' => 'favorite_id',
            'associationForeignKey' => 'user_id',
            'conditions' => '',
            'order' => '',
            'limit' => '',
            'unique' => true,
            'finderQuery' => '',
            'deleteQuery' => '',
            'insertQuery' => ''
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
        
        // TODO HACK SPOTTED the main things here is favorite, not user
        // so the query should be reviewed moreover it's all but optimized 
        $user = new User();
        $user->id = $user_id;
        $user->hasAndBelongsToMany['Favorite']['limit'] = null;
        $user = $user->read();

        return $user;
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
        return $this->Favorite->habtmAdd(
            'User',
            $sentenceId,
            $userId
        );
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
        return $this->Favorite->habtmDelete(
            'User',
            $sentenceId,
            $userId
        );
    } 
}
?>
