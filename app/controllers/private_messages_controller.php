<?php
/*
    Tatoeba Project, free collaborativ creation of languages corpuses project
    Copyright (C) 2009 Etienne Deparis <etienne.deparis@umaneti.net>

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

	var $helpers = array('Comments', 'Languages', 'Tooltip', 'Navigation', 'Html', 'Date');
	var $components = array ('GoogleLanguageApi', 'Permissions', 'Mailer');

	var $langs = array('en', 'fr', 'jp', 'es', 'de');

	function beforeFilter() {
	    parent::beforeFilter();
	    $this->Auth->allowedActions = array('*');
	}

	function index(){
		$this->redirect(array('action' => 'folder', 'Inbox'));
	}

	function folder($folder_id = 'Inbox'){

		if($folder_id == 'Sent'){
			$inboxes = $this->PrivateMessage->find(
				'all',
				array(
					'conditions' => array('PrivateMessage.sender' => $this->Auth->user('id'),
											'PrivateMessage.folder' => $folder_id),
					'limit'=> 10,
					'order' => 'PrivateMessage.date DESC'
				)
			);
		}else{
			$inboxes = $this->PrivateMessage->find(
					'all',
					array(
						'conditions' => array('PrivateMessage.recpt' => $this->Auth->user('id'),
												'PrivateMessage.folder' => $folder_id),
						'limit'=> 10,
						'order' => 'PrivateMessage.date DESC'
					)
				);
		}

		$content = array();

		foreach($inboxes as $m){
			$tou = new User();
			$tou->id = $m['PrivateMessage']['sender'];
			$tou = $tou->read();
			$content[] = array(
				'from' => $tou['User']['username'],
				'title' => $m['PrivateMessage']['title'],
				'id' => $m['PrivateMessage']['id'],
				'date' => $m['PrivateMessage']['date']
			);
		}

		$this->pageTitle = __('Private Messages - ', true) . $folder_id;
		$this->set('folder', __($folder_id, true));
		$this->set('content', $content);
	}

	function send(){
		if(!empty($this->data)){
			$this->data['PrivateMessage']['sender'] = $this->Auth->user('id');

			$this->PrivateMessage->User->recursive = 0;
			$tou = $this->PrivateMessage->User->findByUsername($this->data['PrivateMessage']['recpt']);
			$this->data['PrivateMessage']['recpt'] = $tou['User']['id'];
			$this->data['PrivateMessage']['folder'] = 'Inbox';
			$this->data['PrivateMessage']['date'] = date("Y-m-d h:i:s", time());
			$this->PrivateMessage->save($this->data);

			$this->PrivateMessage->id = null;
			$this->data['PrivateMessage']['folder'] = 'Sent';
			$this->PrivateMessage->save($this->data);
		}
		$this->redirect(array('action' => 'folder', 'Sent'));
	}

	function show($mid){

		$message = $this->PrivateMessage->findById($mid);

		$tou = new User();
		$tou->id = $message['PrivateMessage']['sender'];
		$tou = $tou->read();
		$content = array(
			'from' => $tou['User']['username'],
			'title' => $message['PrivateMessage']['title'],
			'content' => $message['PrivateMessage']['content'],
			'id' => $message['PrivateMessage']['id'],
			'date' => $message['PrivateMessage']['date']
		);

		$this->pageTitle = __('Private Messages - ', true) . $content['title'] . __(' from ', true) . $content['from'];
		$this->set('content', $content);

	}

	function create($toid = ''){
		if($toid != '') $this->set('toid', $toid);
		else $this->set('toid', '');
	}

}
?>
