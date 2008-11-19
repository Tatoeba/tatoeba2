<?php
class LatestActivitiesController extends AppController {

	var $name = 'LatestActivities';
	var $helpers = array('Html', 'Form');

	function beforeFilter() {
		parent::beforeFilter(); 
		
		$this->Auth->allowedActions = array('index');
	}
	
	function index() {
		$this->LatestActivity->recursive = 0;
		$this->set('latestActivities', $this->paginate());
	}

}
?>