<?php
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
		$s->id = $sentence_id;		
		$s->recursive = 2;
		$sentence = $s->read();
		$this->set('sentence', $sentence);		
		
		// checking which options user can access to
		$specialOptions = $this->Permissions->getSentencesOptions($sentence['Sentence']['user_id'], $this->Auth->user('id'));
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
			'conditions' => array('Contribution.user_id !=' => null),
			'group' => array('Contribution.user_id'),
			'order' => 'total DESC'
		));
		$this->set('stats', $stats);
	}

}
?>