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

/**
 * Model for favoritedBy.
 *
 * @category FavoritedBy
 * @package  Models
 * @author   Allan Simon <allan.simon@supinfo.com>
 * @license  Affero General Public License
 * @link     http://tatoeba.org
 */

class FavoritedBy extends AppModel
{
    public $name = 'FavoritedBy';
    public $useTable = 'favorites_users';
    public $actsAs = array('Containable');


    /**
     * Indicates whether a sentence has been favorited by a user or not.
     *
     * @param int $sentenceId Id of the sentence.
     * @param int $userId     Id of the user.
     *
     * @param void
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
?>
