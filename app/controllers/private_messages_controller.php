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

	// We don't use index at all : by default, we just display the inbox folder to the user
	function index(){
		$this->redirect(array('action' => 'folder', 'Inbox'));
	}

	/* Function which will display the folders to the user.
	 * The folder name is given in parameters, as messages are stored by folder name in
	 * the database (SQL ENUM)
	 */
	function folder($folderId = 'Inbox'){


		// Workaround to change criterium if we want the sent folder
		if($folderId == 'Sent'){
			$inboxes = $this->PrivateMessage->find(
				'all',
				array(
					'conditions' => array('PrivateMessage.sender' => $this->Auth->user('id'),
											'PrivateMessage.folder' => $folderId),
					'limit'=> 10,
					'order' => 'PrivateMessage.date DESC'
				)
			);
		}else{
			$inboxes = $this->PrivateMessage->find(
					'all',
					array(
						'conditions' => array('PrivateMessage.recpt' => $this->Auth->user('id'),
												'PrivateMessage.folder' => $folderId),
						'limit'=> 10,
						'order' => 'PrivateMessage.date DESC'
					)
				);
		}

		$content = array();

		foreach($inboxes as $m){
			$toUser = new User();
			$toUser->id = $m['PrivateMessage']['sender'];
			$toUser = $toUser->read();
			$content[] = array(
				'from' => $toUser['User']['username'],
				'title' => $m['PrivateMessage']['title'],
				'id' => $m['PrivateMessage']['id'],
				'date' => $m['PrivateMessage']['date'],
				'isnonread' => $m['PrivateMessage']['isnonread']
			);
		}

		$this->pageTitle = __('Private Messages - ', true) . $folderId;
		$this->set('folder', $folderId);
		$this->set('content', $content);
	}

	/* This function has to send the message, then to display the sent folder
	 */
	function send(){
		if(!empty($this->data)){
			$this->data['PrivateMessage']['sender'] = $this->Auth->user('id');

			$this->PrivateMessage->User->recursive = 0;
			$toUser = $this->PrivateMessage->User->findByUsername($this->data['PrivateMessage']['recpt']);
			$this->data['PrivateMessage']['recpt'] = $toUser['User']['id'];
			$this->data['PrivateMessage']['folder'] = 'Inbox';
			$this->data['PrivateMessage']['date'] = date("Y-m-d h:i:s", time());
			$this->data['PrivateMessage']['isnonread'] = 1;
			$this->PrivateMessage->save($this->data);

			$this->PrivateMessage->id = null;
			$this->data['PrivateMessage']['folder'] = 'Sent';
			$this->data['PrivateMessage']['isnonread'] = 0;
			$this->PrivateMessage->save($this->data);
		}
		$this->redirect(array('action' => 'folder', 'Sent'));
	}

	/* Function to show the content of a message */
	function show($messageId){

		/* The following lines of code check if a message is read, or not
		 * and change is read value automatically.
		 */
		$message = $this->PrivateMessage->findById($messageId);
		if($message['PrivateMessage']['isnonread'] == 1){
			$message['PrivateMessage']['isnonread'] = 0;
			$this->PrivateMessage->save($message);
		}

		$toUser = new User();
		$toUser->id = $message['PrivateMessage']['sender'];
		$toUser = $toUser->read();
		$content = array(
			'from' => $toUser['User']['username'],
			'title' => $message['PrivateMessage']['title'],
			'content' => $message['PrivateMessage']['content'],
			'id' => $message['PrivateMessage']['id'],
			'date' => $message['PrivateMessage']['date'],
			'isnonread' => $message['PrivateMessage']['isnonread']
		);

		$this->pageTitle = __('Private Messages - ', true) . $content['title'] . __(' from ', true) . $content['from'];
		$this->set('content', $content);

	}

	// Delete message function
	function delete($folder, $messageId){
		$message = $this->PrivateMessage->findById($messageId);
		$message['PrivateMessage']['folder'] = 'Trash';
		$this->PrivateMessage->save($message);
		$this->redirect(array('action' => 'folder', $folder));
	}

	// Generalistic read/unread marker function.
	function mark($folder, $messageId){
		$message = $this->PrivateMessage->findById($messageId);
		switch($message['PrivateMessage']['isnonread']){
			case 1 : $message['PrivateMessage']['isnonread'] = 0;break;
			case 0 : $message['PrivateMessage']['isnonread'] = 1;break;
		}
		$this->PrivateMessage->save($message);
		$this->redirect(array('action' => 'folder', $folder));
	}

	// Create a new message
	function create($toUserId = ''){
		if($toUserId != '') $this->set('toid', $toUserId);
		else $this->set('toid', '');
	}

	/* No view behind this function, which aim is to inform the user
	 * how many unread messages stay on his inbox.
	 * This function is called in top1.ctp
	 */
	function check(){
		if($this->Auth->user('id')){
			return $this->PrivateMessage->find(
				'count',
				array(
					'conditions' => array('PrivateMessage.recpt' => $this->Auth->user('id'),
											'PrivateMessage.folder' => 'Inbox',
											'PrivateMessage.isnonread' => 1)
				)
			);
		}
		return 0;
	}

}
?>
