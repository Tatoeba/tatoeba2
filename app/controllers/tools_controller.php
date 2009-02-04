<?php
class ToolsController extends AppController {

	var $name = 'Tools';
	var $helpers = array('Kakasi');
	
	function beforeFilter() {
	    parent::beforeFilter();
		
		// setting actions that are available to everyone, even guests
	    $this->Auth->allowedActions = array('*');
	}
	
	function index(){
	}
	
	function kakasi(){
	}
}