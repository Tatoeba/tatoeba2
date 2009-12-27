<?php
/*
    Tatoeba Project, free collaborative creation of multilingual corpuses project
    Copyright (C) 2009  HO Ngoc Phuong Trang <tranglich@gmail.com>

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


App::import('Core', 'Sanitize');

class ContributionsController extends AppController {

	var $name = 'Contributions';
	var $helpers = array('Html', 'Form', 'Sentences', 'Logs', 'Navigation', 'Date','languages');
	var $components = array('Permissions');

	function beforeFilter() {
		parent::beforeFilter(); 
		
		$this->Auth->allowedActions = array('*');
	}
	
	/**
	 * Display 200 last contributions.
     * possibility to filter on a specific language
     * send as 1st parameter
	 */
	function index($filter = 'und' ) {
		$this->set('contributions', $this->Contribution->getLastContributions(200,$filter));
	}

	/**
	 * Return 10 last contributions.
	 * Called with requestAction on homepage.
	 */
	function latest(){
		return $this->Contribution->getLastContributions(10);
	}
	
	
	/**
	 * Display number of contributions for each member.
	 */
	function statistics(){
		$this->set('stats', $this->Contribution->getUsersStatistics());
	}
	
	/**
	 * Display number of contributions for each day.
	 */
	function activity_timeline(){
		$this->set('stats', $this->Contribution->getActivityTimelineStatistics());
	}
}
?>
