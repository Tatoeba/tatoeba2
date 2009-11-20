<?php
/*
    Tatoeba Project, free collaborative creation of multilingual corpuses project
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


App::import('Core', 'Sanitize');

class PrivateMessagesController extends AppController {
	var $name = 'PrivateMessages';

	var $helpers = array('Comments', 'Languages', 'Navigation', 'Html', 'Date');//, 'PrivateMessages');
	var $components = array ('GoogleLanguageApi', 'Permissions', 'Mailer');

	var $langs = array('en', 'fr', 'jp', 'es', 'de');

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
		$inboxes = $this->PrivateMessage->find(
			'all',
			array(
				'conditions' => array('PrivateMessage.user_id' => $this->Auth->user('id'),
										'PrivateMessage.folder' => $folderId),
				'limit'=> 10,
				'order' => 'PrivateMessage.date DESC'
			)
		);

		$content = array();

		foreach($inboxes as $message){
			$toUser = new User();
			$toUser->id = $message['PrivateMessage']['sender'];
			$toUser = $toUser->read();


			if($message['PrivateMessage']['title'] == '')
				$messageTitle = __('[no subject]', true);
			else
				$messageTitle = $message['PrivateMessage']['title'];

			$content[] = array(
				'from' => $toUser['User']['username'],
				'title' => $messageTitle,
				'id' => $message['PrivateMessage']['id'],
				'date' => $message['PrivateMessage']['date'],
				'isnonread' => $message['PrivateMessage']['isnonread']
			);
		}

		$this->pageTitle = __('Private Messages - ', true) . $folderId;
		$this->set('folder', $folderId);
		$this->set('content', $content);
	}

	/* This function has to send the message, then to display the sent folder
	 */
	function send(){
        Sanitize::html($this->data['PrivateMessage']['recpt']);
        Sanitize::html($this->data['PrivateMessage']['send']);
        Sanitize::html($this->data['PrivateMessage']['content']);

		if(!empty($this->data['PrivateMessage']['recpt']) && !empty($this->data['PrivateMessage']['content'])){
			$this->data['PrivateMessage']['sender'] = $this->Auth->user('id');
          // TODO add a check if the user to send doesn't exist 
			$this->PrivateMessage->User->recursive = 0;
			$toUser = $this->PrivateMessage->User->findByUsername($this->data['PrivateMessage']['recpt']);
			$this->data['PrivateMessage']['recpt'] = $toUser['User']['id'];
			$this->data['PrivateMessage']['user_id'] = $toUser['User']['id'];
			$this->data['PrivateMessage']['folder'] = 'Inbox';
			$this->data['PrivateMessage']['date'] = date("Y-m-d h:i:s", time());
			$this->data['PrivateMessage']['isnonread'] = 1;
			$this->PrivateMessage->save($this->data);

			$this->PrivateMessage->id = null;
			$this->data['PrivateMessage']['user_id'] = $this->Auth->user('id');
			$this->data['PrivateMessage']['folder'] = 'Sent';
			$this->data['PrivateMessage']['isnonread'] = 0;
			$this->PrivateMessage->save($this->data);
			$this->redirect(array('action' => 'folder', 'Sent'));
		}else{
			$this->redirect(array('action' => 'write'), $this->data['PrivateMessage']['recpt'], 'error');
		}
	}

	/* Function to show the content of a message */
	function show($messageId){

        Sanitize::paranoid($messageId);
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
		if($message['PrivateMessage']['title'] == '')
			$messageTitle = __('[no subject]', true);
		else
			$messageTitle = $message['PrivateMessage']['title'];
		$content = array(
			'from' => $toUser['User']['username'],
			'title' => $messageTitle,
			'content' => nl2br($message['PrivateMessage']['content']),
			'id' => $message['PrivateMessage']['id'],
			'date' => $message['PrivateMessage']['date'],
			'isnonread' => $message['PrivateMessage']['isnonread'],
			'folder' => $message['PrivateMessage']['folder']
		);

		$this->pageTitle = __('Private Messages - ', true) . $content['title'] . __(' from ', true) . $content['from'];
		$this->set('content', $content);

	}

	// Delete message function
	function delete($folder, $messageId){
        Sanitize:: paranoid($messageId);
		$message = $this->PrivateMessage->findById($messageId);
		$message['PrivateMessage']['folder'] = 'Trash';
		$this->PrivateMessage->save($message);
		$this->redirect(array('action' => 'folder', $folder));
	}

	// Restore message function
	function restore($messageId){
         
        Sanitize:: paranoid($messageId);
		$message = $this->PrivateMessage->findById($messageId);

		if($message['PrivateMessage']['recpt'] == $this->Auth->user('id'))
			$folder = 'Inbox';
		else $folder = 'Sent';

		$message['PrivateMessage']['folder'] = $folder;
		$this->PrivateMessage->save($message);
		$this->redirect(array('action' => 'folder', $folder));
	}

	// Generalistic read/unread marker function.
	function mark($folder, $messageId){
        Sanitize:: paranoid($messageId);
		$message = $this->PrivateMessage->findById($messageId);
		switch($message['PrivateMessage']['isnonread']){
			case 1 : $message['PrivateMessage']['isnonread'] = 0;break;
			case 0 : $message['PrivateMessage']['isnonread'] = 1;break;
		}
		$this->PrivateMessage->save($message);
		$this->redirect(array('action' => 'folder', $folder));
	}

	// Create a new message
	function write($toUserLogin = '', $replyToMessageId = null){

        
        Sanitize::html($toUserLogin);
        Sanitize::paranoid($replyToMessageId);
		if($replyToMessageId != null){
			$message = $this->PrivateMessage->findById($replyToMessageId);
			if($message['PrivateMessage']['title'] == '')
				$messageTitle = __('Re: [no subject]', true);
			else
				$messageTitle = 'Re: ' . $message['PrivateMessage']['title'];
			$this->set('replyToTitle', $messageTitle);
			$messNextRegExp = preg_replace("#\r?\n#iU", " ", $message['PrivateMessage']['content']);
			$messNextRegExp = preg_replace("#\r?\n#iU", "\n > ", wordwrap($messNextRegExp, 50));
			$this->set('replyToContent', "\n" . $toUserLogin . __(' wrote:', true) . "\n > " . $messNextRegExp);
		}else if($replyToMessageId == 'error'){
			$this->set('errorString', __('You must fill at least the "To" field and the content field.', true));
		}else{
			$this->set('replyToContent', '');
			$this->set('replyToTitle', '');
		}

		if($toUserLogin != '') $this->set('toUserLogin', $toUserLogin);
		else $this->set('toUserLogin', '');
	}

	/* No view behind this function, which aim is to inform the user
	 * how many unread messages stay on his inbox.
	 * This function is called in top1.ctp
	 */
	function check(){
		return $this->PrivateMessage->find(
			'count',
			array(
				'conditions' => array('PrivateMessage.recpt' => $this->Auth->user('id'),
										'PrivateMessage.folder' => 'Inbox',
										'PrivateMessage.isnonread' => 1)
			)
		);
	}

}
?>
