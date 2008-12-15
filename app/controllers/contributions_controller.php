<?php
class ContributionsController extends AppController {

	var $name = 'Contributions';
	var $helpers = array('Html', 'Form', 'Sentences');
	var $components = array('Permissions');

	function beforeFilter() {
		parent::beforeFilter(); 
		
		$this->Auth->allowedActions = array('*');
	}
	
	function index() {
		$this->LatestActivity->recursive = 0;
		$this->set('contributions', $this->paginate());
	}
	
	function show($sentence_id){
		$sentence = new Sentence();
		$sentence->id = $sentence_id;		
		$sentence->recursive = 2;
		$this->set('sentence', $sentence->read());		
		
		// checking which options user can access to
		$specialOptions = $this->Permissions->getSentencesOptions();
		$this->set('specialOptions',$specialOptions);
	}
	
	function latest(){
		$this->Contribution->recursive = 2;
		return $this->Contribution->find('all', array('limit' => 10));
	}

}
?>