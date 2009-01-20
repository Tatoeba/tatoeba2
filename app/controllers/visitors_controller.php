<?php
class VisitorsController extends AppController {

	var $name = 'Visitors';
	var $webRobots = array(
		// google
		  '66.249.71.85'
		, '66.249.71.83'
		, '66.249.71.84'
		, '66.249.67.2'
		// yahoo
		, '72.30.79.84'
	);
	
	function beforeFilter() {
		parent::beforeFilter(); 
		
		// setting actions that are available to everyone, even guests
		// no need to allow login
		$this->Auth->allowedActions = array('*');
	}
	
	function online(){
		// delete users with timestamp higer than 5 minutes
		$timestamp_5min = time() - (60 * 5);
		$this->Visitor->deleteAll(array('timestamp < ' . $timestamp_5min),false);
		
		// adding visitor to the list
		if($this->Visitor->findByIp($_SERVER['REMOTE_ADDR']) == null){
			$this->data['Visitor']['timestamp'] = time();
			$this->data['Visitor']['ip'] = $_SERVER['REMOTE_ADDR'];
			$this->Visitor->save($this->data);
		}
		
		return count($this->Visitor->findAll());
	}
	
}	
?>