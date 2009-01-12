<?php
class ContributionsController extends AppController {

	var $name = 'Contributions';
	var $helpers = array('Html', 'Form', 'Sentences', 'Logs', 'Tooltip');
	var $components = array('Permissions');

	function beforeFilter() {
		parent::beforeFilter(); 
		
		$this->Auth->allowedActions = array('*');
	}
	
	function index() {
		$limit = 200;
		$this->set('contributions', $this->Contribution->find('all', array('limit' => $limit)));
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
		return $this->Contribution->find('all', array('limit' => 10));
	}

}
?>