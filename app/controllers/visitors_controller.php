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
class VisitorsController extends AppController {

	var $name = 'Visitors';
	var $webRobotsIps = array(
		// google
		  '66.249.71.85'
		, '66.249.71.83'
		, '66.249.71.84'
		, '66.249.71.2'
		, '66.249.67.2'
		, '66.249.71.1'
		, '66.249.71.3'
		, '66.249.70.78'
		, '66.249.71.22'
		, '66.249.71.24'
		, '66.249.67.39'
		// yahoo
		, '72.30.79.84'
		, '67.195.37.109'
		, '67.195.37.92'
		, '67.195.37.125'
		, '72.30.87.118'
		// msn-bot
		, '65.55.210.178'
		, '65.55.107.221'
		, '65.55.210.176'
		, '65.55.210.163'
		, '65.55.210.180'
		, '65.55.210.173'
		, '65.55.210.172'
		, '65.55.110.171'
		, '65.55.210.181'
		// other
		, '61.247.222.52'
		, '61.247.222.53'
		, '61.247.222.54'
		, '61.247.222.55'
		, '61.247.222.56'
		, '208.36.144.7'
		, '193.47.80.47'
		, '208.36.144.7'
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
		if(!in_array($_SERVER['REMOTE_ADDR'], $this->webRobotsIps)){
			if($this->Visitor->findByIp($_SERVER['REMOTE_ADDR']) == null){
				$this->data['Visitor']['timestamp'] = time();
				$this->data['Visitor']['ip'] = $_SERVER['REMOTE_ADDR'];
				$this->Visitor->save($this->data);
			}
		}
		
		return $this->Visitor->find('count');
	}
	
}	
?>
