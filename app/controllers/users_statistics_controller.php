<?php
/*
    Tatoeba Project, free collaborativ creation of languages corpuses project
    Copyright (C) 2009  TATOEBA Project(should be changed)

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU Affero General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU Affero General Public License for more details.

    You should have received a copy of the GNU Affero General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/
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
