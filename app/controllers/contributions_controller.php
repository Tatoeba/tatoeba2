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
class ContributionsController extends AppController {

	var $name = 'Contributions';
	var $helpers = array('Html', 'Form', 'Sentences', 'Logs', 'Tooltip', 'Navigation', 'Date');
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
	
	function show($sentence_id){
		$s = new Sentence();
		
		if($sentence_id == "random"){
			$resultMax = $s->query('SELECT MAX(id) FROM sentences');
			$max = $resultMax[0][0]['MAX(id)'];
			
			$randId = rand(1, $max);
			$this->redirect(array("action"=>"show", $randId));
		}	
		
		$s->id = $sentence_id;
		$s->recursive = 2;
		$sentence = $s->read();
		
		if($sentence != null){
			$this->set('sentenceExists', true);
			$this->set('sentence', $sentence);
		}else{
			$this->set('sentenceExists', false);
			$this->Contribution->unbindModel(
				array(
					'belongsTo' => array('Sentence')
				)
			);
			$contributions = $this->Contribution->find('all', 
				array(
					'conditions' => array('Contribution.sentence_id' => $sentence_id),
					'order' => 'Contribution.datetime DESC'
				)
			);
			$this->set('contributions', $contributions);
		}
		
		// checking which options user can access to
		$specialOptions = $this->Permissions->getSentencesOptions($sentence, $this->Auth->user('id'));
		$this->set('specialOptions',$specialOptions);
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
	
	function statistics(){
		//Configure::write('debug',2);
		$this->Contribution->unbindModel(
			array(
				'belongsTo' => array('Sentence')
			)
		);
		$this->Contribution->recursive = 0;
		$stats = $this->Contribution->find('all', array(
			'fields' => array('Contribution.user_id', 'User.id', 'User.username', 'User.since', 'User.group_id', 'COUNT(*) as total'),
			'conditions' => array('Contribution.user_id !=' => null, 'Contribution.type' => 'sentence'),
			'group' => array('Contribution.user_id'),
			'order' => 'total DESC'
		));
		$this->set('stats', $stats);
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
