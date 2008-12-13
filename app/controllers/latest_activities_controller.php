<?php
class LatestActivitiesController extends AppController {

	var $name = 'LatestActivities';
	var $helpers = array('Html', 'Form', 'Sentences');
	var $components = array('Permissions');

	function beforeFilter() {
		parent::beforeFilter(); 
		
		$this->Auth->allowedActions = array('*');
	}
	
	function index() {
		$this->LatestActivity->recursive = 0;
		$this->set('latestActivities', $this->paginate());
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

}
?>