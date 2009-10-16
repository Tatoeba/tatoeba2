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
class PrivateMessagesController extends AppController {
	var $name = 'PrivateMessages';

	var $helpers = array('Comments', 'Languages', 'Tooltip', 'Navigation', 'Html');
	var $components = array ('GoogleLanguageApi', 'Permissions', 'Mailer');

	var $langs = array('en', 'fr', 'jp', 'es', 'de');

	function beforeFilter() {
	    parent::beforeFilter();
	    $this->Auth->allowedActions = array('*');
	}

	function index(){


		$inboxes = $this->PrivateMessage->find(
				"all",
				array(
					"conditions" => array("PrivateMessage.recpt" => $this->Auth->user('id')),
					"limit"=> 10,
					"order" => "PrivateMessage.date DESC"
				)
			);

		$inbox = array();

		foreach($inboxes as $m){
			$tou = new User();
			$tou->id = $m['PrivateMessage']['sender'];
			$tou = $tou->read();
			$inbox[] = array(
				'from' => $tou['User']['username'],
				'to' => $this->Auth->user('username'),
				'title' => $m['PrivateMessage']['title'],
				'content' => $m['PrivateMessage']['content'],
				'date' => $m['PrivateMessage']['date']
			);
		}

		$this->set('inboxes', $inbox);
	}

	function send($user_id){
		$message = new message();
	}

	function show($sentence_id){

	}

	function save(){
	}

}
?>
