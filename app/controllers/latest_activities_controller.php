<?php
class LatestActivitiesController extends AppController {

	var $name = 'LatestActivities';
	var $helpers = array('Html', 'Form');

	function index() {
		$this->LatestActivity->recursive = 0;
		$this->set('latestActivities', $this->paginate());
	}

}
?>