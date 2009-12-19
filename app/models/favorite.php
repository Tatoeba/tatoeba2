<?php
/*
    Tatoeba Project, free collaborativ creation of languages corpuses project
    Copyright (C) 2009  TATOEBA Project(should be changed)

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU Affero General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU Affero General Public License for more details.

    You should have received a copy of the GNU Affero General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

class Favorite extends AppModel{
	var $name = 'Favorite';
	var $useTable = 'sentences';

	var $actsAs = array('ExtendAssociations');


	var $hasAndBelongsToMany = array(
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
    /*
    ** get number of favorite sentence of a user
    */
    function numberOfFavoritesOfUser($userId){
        $result = $this->query("
            SELECT count(user_id) AS numberOfFavorites FROM favorites_users
            WHERE user_id = $userId
        ");

        return $result[0][0]["numberOfFavorites"];
    }

}
?>
