<?php
class UsersStatisticsController extends AppController {

	var $name = 'UsersStatistics';
	var $helpers = array('Html', 'Form');

	function beforeFilter() {
	    parent::beforeFilter(); 
		
		// setting actions that are available to everyone, even guests
	    $this->Auth->allowedActions = array('*');
	}
	
	
	function index() {
		$this->UsersStatistic->recursive = 0;
		$this->set('usersStatistics', $this->paginate());
	}
}
?>