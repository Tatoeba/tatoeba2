<?php
/*
    Tatoeba Project, free collaborative creation of multilingual corpuses project
    Copyright (C) 2009  HO Ngoc Phuong Trang <tranglich@gmail.com>

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
        
    /*TODO*/
    function getContributionsRelatedToSentence($sentenceId){
        $result = $this->find(
            'all',
            array(
                'fields' => array(
                    'Contribution.text',
                    'Contribution.translation_id',
                    'Contribution.action',
                    'Contribution.id',
                    'Contribution.datetime',
                    'User.username',
                    'User.id'
                ),
                'conditions' => array (
                    'Contribution.sentence_id' => $sentenceId     
                ),
                'contains' => array (
                    'User'=> array(
                        'fields' => array('User.username','User.id')
                    ),
                )
            )
        );
        return $result ;
    }
	
	/**
	 * Get last contributions.
	 */
	function getLastContributions($limit){
		$this->recursive = 0;
		$this->unbindModel(
			array(
				'belongsTo' => array('Sentence')
			)
		);
		return $this->find('all', 
			array(
				'conditions' => array('Contribution.type' => 'sentence'),
				'limit' => $limit, 'order' => 'Contribution.datetime DESC'
			)
		);
	}
	
	/**
	 * Returns number of contributions for each member,
	 * ordered from the highest contributor to the lowest.
	 */	
	function getUsersStatistics(){
		$this->unbindModel(
			array(
				'belongsTo' => array('Sentence')
			)
		);
		$this->recursive = 0;
		$query = array(
			'fields' => array(
				'Contribution.user_id', 'User.id', 'User.username'
				, 'User.since', 'User.group_id', 'COUNT(*) as total'
			),
			'conditions' => array(
				'Contribution.user_id !=' => null
				, 'Contribution.type' => 'sentence'
			),
			'group' => array('Contribution.user_id'),
			'order' => 'total DESC'
		);
		return $this->find('all', $query);
	}	
	
	
	/**
	 * Returns number of contributions for each day.
	 * We only count the number of new sentences, not the
	 * number of modifications.
	 */
	function getActivityTimelineStatistics(){
		$this->Contribution->recursive = 0;
		return $this->find('all', array(
			'fields' => array(
				'Contribution.datetime'
				, 'COUNT(*) as total'
				, 'date_format(datetime,\'%b %D %Y\') as day'
			),
			'conditions' => array(
				'Contribution.datetime > \'2008-01-01 00:00:00\''
				, 'Contribution.translation_id' => null
				, 'Contribution.action' => 'insert'
			),
			'group' => array('day'),
			'order' => 'Contribution.datetime DESC'
		));
	}
}
?>
