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

class Contribution extends AppModel {

	var $name = 'Contribution';
	var $actsAs = array("Containable");
	var $belongsTo = array('Sentence', 'User');
    
    /*
    ** get number of contributions made by a given user
    */
    function numberOfContributionsBy($userId){
        return $this->find(
            'count',
            array(
                'conditions' => array( 'Contribution.user_id' => $userId)
                )
            );

    }

    /*
    ** get the Xth best contributors
    */
    function getTopContributors($limit){
        $result = $this->find(
            'all',
            array(
                'order' => 'total DESC',
                'fields' => array(
                    'COUNT(Contribution.id) AS total',
                    'User.username',
                    'User.group_id'
                ),
                'group' => 'Contribution.user_id',
                'conditions' => array (
                    'Contribution.user_id !=' => null,
                    'Contribution.type' => 'sentence',
                    'User.group_id <' => 5
                ),
                'limit' => $limit ,
                'contains' => array (
                    'User' => array (
                        'fields' => array( 'User.username', 'User.group_id'),
                    ),
                
                ) 
            )
        );
        return $result; 

    }


}
?>
