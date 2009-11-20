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


App::import('Core', 'Sanitize');

class ContributionsController extends AppController {

	var $name = 'Contributions';
	var $helpers = array('Html', 'Form', 'Sentences', 'Logs', 'Navigation', 'Date');
	var $components = array('Permissions');

	function beforeFilter() {
		parent::beforeFilter(); 
		
		$this->Auth->allowedActions = array('*');
	}
	
	function index() {
		$limit = 200;
		$this->Contribution->unbindModel(
			array(
				'belongsTo' => array('Sentence')
			)
		);
		$this->set('contributions', $this->Contribution->find('all', 
				array(
					'conditions' => array('Contribution.type' => 'sentence'),
					'limit' => $limit, 'order' => 'Contribution.datetime DESC'
				)
			)
		);
	}

	
	function latest(){
		$this->Contribution->unbindModel(
			array(
				'belongsTo' => array('Sentence')
			)
		);
		$this->Contribution->recursive = 0;
		return $this->Contribution->find('all', array(
				'conditions' => array('Contribution.type' => 'sentence'),
				'limit' => 10, 'order' => 'Contribution.datetime DESC'
			)
		);
	}
	
	function statistics($return = null){
		$this->Contribution->unbindModel(
			array(
				'belongsTo' => array('Sentence')
			)
		);
		$this->Contribution->recursive = 0;
		$query = array(
			'fields' => array('Contribution.user_id', 'User.id', 'User.username', 'User.since', 'User.group_id', 'COUNT(*) as total'),
			'conditions' => array('Contribution.user_id !=' => null, 'Contribution.type' => 'sentence'),
			'group' => array('Contribution.user_id'),
			'order' => 'total DESC'
		);
		if($return != null){
			$query['limit'] = 20;
			$query['conditions']['User.group_id <'] = 5; 
		}
		$stats = $this->Contribution->find('all', $query);
		
		if($return == 0 OR $return == null){
			$this->set('stats', $stats);
		}else{
			return $stats;
		}
	}
	
	function activity_timeline(){
		$this->Contribution->recursive = 0;
		$stats = $this->Contribution->find('all', array(
			'fields' => array('Contribution.datetime', 'COUNT(*) as total', 'date_format(datetime,\'%b %D %Y\') as day'),
			'conditions' => array('Contribution.datetime > \'2008-01-01 00:00:00\'', 'Contribution.translation_id' => null, 'Contribution.action' => 'insert'),
			'group' => array('day'),
			'order' => 'Contribution.datetime DESC'
		));
		$this->set('stats', $stats);
	}
}
?>
