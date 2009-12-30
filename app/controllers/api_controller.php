<?php
/*
    Tatoeba Project, free collaborative creation of multilingual corpuses project
    Copyright (C) 2009  Salem BEN YAALA <salem.benyaala@gmail.com>

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

class ApiController extends AppController {

	var $name = 'Api';

	var $components = array('RequestHandler');

	/**
	 * What you can request via API
	 *
	 * @var array
	 * @access private
	 */
	private $aWhat = array('user', 'sentence', 'comment', 'message');

	/**
	 * Array of useful HTTP return code
	 *
	 * @todo Complete the list if needed
	 * @var array
	 * @access private
	 */
	private $aCodeMsgs = array(
		200 => 'OK', // Display or edit User/Sentence/Comment
		201 => 'Created', // Add User/Sentence/Comment

		301 => 'Moved Permanently',
		304 => 'Not Modified',

		400 => 'Bad Request', // Malformed query
		401 => 'Unauthorized', // Bad username/password
		404 => 'Not Found', // User/Sentence/Comment not found
		403 => 'Forbidden', // Forbiden action/query
		408 => 'Request Timeout',
		410 => 'Gone',

		500 => 'Internal Server Error',
		501 => 'Not Implemented', // function not implemented yet
		503 => 'Service Unavailable'
	);

	/**
	 *
	 * @todo Restrict actions if needed. I don't know much about this stuff.
	 */
	function beforeFilter() {
		parent::beforeFilter();

		$this->Auth->allowedActions = array('*');
	}

	/**
	 * Select output format
	 *
	 * @param string $sFormat Output format. It may be XML, JSON or PHP
	 * @access private
	 */
	private function doHeader($sFormat) {
		switch ($sFormat) {
			case 'xml':
				$this->header('content-type: text/xml');
			break;
			case 'json':
				// ?
			break;
			case 'php':
				// useful for PHP applications -> deserialize response and then an expoitable PHP array
			break;
			default:
				// XML is default output format
				$this->header('content-type: text/xml');
			break;
		}
	}

	/**
	 * Send the specified view with adequate HTPP code and a friendly message
	 *
	 * @param string $sView View to output
	 * @param integer $iCode HTTP Code
	 * @param string $sMessage Message to display
	 * @access private
	 */
	private function doSend($sView, $iCode, $sMessage){
		$this->layout = null;

		$this->set('code', $iCode);
		$this->set('message', $sMessage);

		$this->render($sView);

		$this->header('HTTP/1.0 ' . $iCode . ' ' . $this->aCodeMsgs[$iCode]);
	}

	/**
	 * Index of documentation
	 *
	 * @example http://totoeba.fr/api/
	 * @access public
	 */
	function index() {
		// Documentation index
		$this->layout = null;
		$this->set('loggedin', ($this->Auth->user('id')) ? true : false);
	}

	/**
	 * Logs in the user via API
	 *
	 * @example http://tatoeba.fr/api/login/ --username --password
	 * @access public
	 */
	function login() {
		$this->doHeader('xml');

		if($this->Auth->user('id')){
			// Already logged in
			$this->doSend('error', 200, 'Already logged in');

			return;
		}

		if(empty($this->data['User'])){
			$this->doSend('error', 403, 'Need username and password to login');

			return;
		}

		if(!$this->Auth->login()){
			$this->doSend('error', 401, 'Wrong username and password');
			
			return;
		}else{
			if($this->Auth->user('group_id') == 5)	{
				$this->doSend('error', 401, 'Your account is not valide');

				return;
			}

			$this->doSend('error', 200, 'Logged in successfully');
			
			return;
		}
	}

	/**
	 * Logs out the user via API
	 *
	 * @example http://tatoeba.fr/api/logout/
	 * @access public
	 */
	function logout() {
		$this->doHeader('xml');
		
		if($this->Auth->user('id')){
			// Logged in
			$this->Auth->logout();
			$this->doSend('error', 200, 'Logged out successfully');
		}else{
			$this->doSend('error', 403, 'Need to be logged in');
		}
	}

	/**
	 * ?
	 *
	 * @param string $sWhat Entity to query
	 * @param string|integer $mID Identifiant of entity to query
	 * @param string $sFormat Output format
	 * 
	 * @example http://tatoeba.fr/api/view/user/socom/xml/
	 * @access public
	 */
	function view($sWhat = null, $mID = null, $sFormat = 'xml') {
		$this->doHeader($sFormat);
		
		if($this->Auth->user('id')){
			// Logged in
			switch ($sWhat) {

				// users
				case 'user':
					$this->loadModel('User');

					$this->User->unBindModel(
						array('hasMany' => array('Contributions', 'Sentences', 'SentenceComments' )
							, 'hasAndBelongsToMany' => array('Favorite')
						)
					);
					$this->User->bindModel(
						array('hasMany' => array('Sentences','SentenceComments' )
							, 'hasAndBelongsToMany' => array (
								'Favorite' => array(
									'className' => 'Favorite',
									'joinTable' => 'favorites_users',
									'foreignKey' => 'user_id',
									'associationForeignKey' => 'favorite_id',
									'unique' => true,
								)
							)
						)
					);

					if(ctype_digit($mID)){ // It is a numerical id
						$aUser = $this->User->findById($mID);
					}elseif(ctype_alnum($mID)){ // It is an alpha-numric id, thus a username
						$aUser = $this->User->findByUsername($mID);
					}else{
						$this->doSend('error', 400, 'Bad Request');
						
						return;
					}

					if(!empty($aUser)){
						if($aUser['User']['is_public']){
							$this->set('user', $aUser);

							$this->doSend('user/view', 200, 'OK');
						}else{
							$this->doSend('error', 401, 'Protected User Profile');
						}
					}else {
						// Send an error
						$this->doSend('error', 404, 'User Not Found');

						return;
					}
				break;

				// sentences
				case 'sentence':
					$this->loadModel('Sentence');

					$sUsername = isset($_GET['u']) ? (string) Sanitize::paranoid($_GET['u']) : null;
					$iUserID = isset($_GET['u_id']) ? (integer) Sanitize::paranoid($_GET['u_id']) : null;

					// $sUsername = Sanitize::paranoid($_GET['u']);
					// $iUserID = (integer) Sanitize::paranoid($_GET['u_id']);
					// $iLimit = (integer) Sanitize::paranoid($_GET['limit']);
					// $iPage = (integer) Sanitize::paranoid($_GET['page']);
					// $iCount = (integer) Sanitize::paranoid($_GET['count']);
					// $iSince = (integer) Sanitize::paranoid($_GET['since']);

					if($mID == null){
						$this->doSend('error', 400, 'Missed Sentence ID');

						return;
					}elseif($mID == 'last'){

						// last X sentences: count=?
						// last X sentences from a user: (u=? or u_id=?) and count=X
						// last X sentences from a user in a language: (u=? or u_id=?) and count=X and lang=?
						// last sentences since a date: since=?
						// last sentences since a date from a user: (u=? or u_id=?) and since=?
						// last sentences since a date from a user in a language: (u=? or u_id=?) and since=? and lang=?

					}elseif ($mID == 'random') {

						if(1){
							// absolutly random
							// absolutly random from a language: lang=?
						}elseif(1){
							// random from a user: u=? or u_id=?
							// random from a user in a language: (u=? or u_id=?) and lang=?
						}else{
							// error
						}

					}elseif (ctype_digit($mID)) {
						$sSentence = $this->Sentence->getShowSentenceWithId($mID);
						$aTranslations = $this->Sentence->getTranslationsOf($mID);

						if($sSentence != null){
							$this->set('sentence', $sSentence);
							$this->set('translations', $aTranslations);

							$this->doSend('sentence/view', 200, 'OK');
						}else{
							$this->doSend('error', 404, 'Sentence Not Found');
						}
					}else {
						$this->doSend('error', 400, 'Bad Request');

						return;
					}
					
				break;

				// comments
				case 'comment':
					$this->doSend('error', 501, 'Not Implemented Yet');
				break;

				// private messages
				case 'message':
					$this->doSend('error', 501, 'Not Implemented Yet');
				break;

				default:
					// Send an error
					$this->doSend('error', 400, 'Bad Request');
				break;

			}
		}else{
			// Guest
			$this->doSend('error', 403, 'Need to be logged in');
		}

	}

	/**
	 * ?
	 *
	 * @param string $sWhat Entity to add
	 */
	function add($sWhat = null) {
		// Sanitize::html($this->data['User']['username']);
		if(!empty($this->data[$sWhat]['data'])){

		}else{
			// Send a error
		}
	}

	/**
	 * ?
	 * 
	 * @param string $sWhat Entity to edit
	 * @param string|integer $mID
	 */
	function edit($sWhat = null, $mID = null) {
		if(!empty($this->data[$sWhat]['data'])){

		}else{
			// Send a error
		}
	}

	/**
	 * ?
	 * 
	 * @param string $sWhat Entity to delete
	 * @param string|integer $mID Identifiant of entity to delete
	 */
	function delete($sWhat = null, $mID = null) {
		$this->loadModel('User');
		
		if($this->Auth->user('id')){
			// Logged in
		}else{
			// Guest
			// Send an error
		}
	}

	function search($sWord = null) {
		;
	}
}
?>
