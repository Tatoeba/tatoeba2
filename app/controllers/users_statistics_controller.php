<?php
class UsersStatisticsController extends AppController {

	var $name = 'UsersStatistics';
	var $helpers = array('Html', 'Form');

	function beforeFilter() {
	    parent::beforeFilter(); 
		
		// setting actions that are available to everyone, even guests
	    // $this->Auth->allowedActions = array('*');
	}
	
	
	function index() {
		$this->UsersStatistic->recursive = -1;
		$userStatistics = $this->UsersStatistic->findAllByUserId($this->Auth->user('id'));		
		$this->set('userStatistics', $userStatistics);
		
		$this->UsersStatistic->recursive = 2;
		$usersStatistics = $this->UsersStatistic->find(
			"all",
			array(
				"fields" => array("SUM(UsersStatistic.quantity) as total", "UsersStatistic.*"),
				"group" => "UsersStatistic.user_id",
				"order" => "total DESC"
			)
		);
		$this->set('usersStatistics', $usersStatistics);
	}
}
?>